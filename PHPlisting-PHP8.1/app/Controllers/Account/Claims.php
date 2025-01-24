<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Account;

class Claims
    extends \App\Controllers\Account\BaseController
{

    public function actionIndex($params)
    {
        if (null === $page = \App\Models\Page::where('slug', 'account/claims')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ((null !== config()->general->maintenance || null === $page->active) && false === auth()->check(['admin_login', 'admin_appearance'])) {
            return redirect(route('maintenance'), 302);
        }

        layout()
            ->setTitle($page->title)
            ->setMeta('title', $page->meta_title)
            ->setMeta('keywords', $page->meta_keywords)
            ->setMeta('description', $page->meta_description);

        if (null === request()->get->get('sort')) {
            request()->get->put('sort', 'id');
            request()->get->put('sort_direction', 'desc');
        }

        $claims = \App\Models\Claim::search()
            ->whereHas('type', function ($query) {
                $query->whereNull('deleted');

                if (false === auth()->check('admin_login')) {
                    $query->whereNotNull('active');
                }
            })
            ->where('user_id', auth()->user()->id)
            ->with([
                'listing',
                'pricing.product',
            ])
            ->paginate();

        $table = dataTable($claims)
            ->addColumns([
                'status' => [__('claim.datatable.label.status'), function ($claim) {
                    return view('misc/status', [
                        'type' => 'claim',
                        'status' => $claim->status,
                    ]);
                }],
                'listing' => [__('claim.datatable.label.listing'), function($claim) {
                    return e($claim->listing->title . ' (id: ' . $claim->listing_id . ')');
                }],
                'pricing' => [__('claim.datatable.label.product'), function($claim) {
                    if (null !== $claim->pricing_id && null !== $claim->pricing) {
                        return e($claim->pricing->getNameWithProduct());
                    }
                }],
                'added_datetime' => [__('claim.datatable.label.added_datetime'), function($claim) {
                    return locale()->formatDatetimeDiff($claim->added_datetime);
                }],
            ]);

        $table
            ->addActions([
                'view' => [__('claim.datatable.action.view_listing'), function ($claim) {
                    return route($claim->listing->type->slug . '/' . $claim->listing->slug);
                }],
            ])
            ->orderColumns([
                'added_datetime',
            ]);

        $form = form()
            ->setMethod('get')
            ->setTemplate('form/inline')
            ->add('status', 'select', [
                'options' => ['' => __('claim.searchform.label.status')] + ['pending' => __('status.label.pending'), 'approved' => __('status.label.approved'), 'rejected' => __('status.label.rejected')],
                'label' => __('claim.searchform.label.status'),
                'weight' => 10,
            ])
            ->add('listing_id', 'listing', [
                'placeholder' => __('claim.searchform.placeholder.listing'),
                'weight' => 20,
            ])
            ->add('submit', 'submit', [
                'label' => __('claim.searchform.label.submit')
            ])
            ->forceRequest();

        $data = collect([
            'page' => $page,
            'html' => view('account/claims/index', [
                'form' => $form,
                'claims' => $table,
                'listing' => (null !== request()->get->get('listing_id') ? \App\Models\Listing::where('id', request()->get->get('listing_id'))->where('user_id', auth()->user()->id)->first() : null),
            ]),
        ]);

        $response = $page->render($data);

        if ($response instanceof \App\Src\Http\RedirectResponse) {
            return $response;
        }

        return response(
            layout()->content($response)
        );
    }

}
