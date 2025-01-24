<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Backlinks
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (false === auth()->check(['admin_content', 'admin_listings'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.backlinks.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'backlinkchecker_datetime';
            request()->get->sort_direction = 'desc';
        }

        $listings = \App\Models\Listing::search(
                (new \App\Models\Listing(['type_id' => $type->id]))
                    ->setSearchable('user_id', 'eq')
                    ->setSortable('backlinkchecker_retry', 'backlinkchecker_retry', null)
                    ->setSortable('backlinkchecker_datetime', 'backlinkchecker_datetime', null),
                [],
                'admin/' . $type->slug . '-invalid-backlinks'
            )
            ->where('type_id', $type->id)
            ->whereNotNull('_backlink')
            ->whereNotNull('backlinkchecker_retry')
            ->with([
                'user',
            ])
            ->paginate();

        $form = form()
            ->setMethod('get')
            ->setTemplate('form/inline')
            ->add('keyword', 'text', [
                'placeholder' => __('admin.listings.searchform.label.keyword'),
                'weight' => 10
            ])
            ->add('user_id', 'user', [
                'placeholder' => __('admin.listings.searchform.label.user'),
                'weight' => 20
            ])
            ->add('submit', 'submit', [
                'label' => __('admin.listings.searchform.label.submit')
            ])
            ->forceRequest();

        return response(layout()->content(
            view('admin/invalid-backlinks/index', [
                'type' => $type,
                'form' => $form,
                'listings' => $this->getTable($listings, $type),
                'alert' => $alert ?? null,
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (false === auth()->check(['admin_content', 'admin_listings'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $listing = \App\Models\Listing::where('id', $params['id'])->where('type_id', $type->id)->whereNotNull('_backlink')->whereNotNull('backlinkchecker_retry')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.backlinks.title.update', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $field = $listing->data->where('field_name', 'website')->first();

        $form = form()
            ->add('title', 'ro', [
                'label' => __('admin.backlinks.form.label.listing_title'), 
                'value' => $listing->title . ' (' . $listing->id . ')',
            ])
            ->add('response_code', 'custom', [
                'label' => __('admin.backlinks.form.label.response_code'), 
                'value' => view('misc/status', ['type' => 'httpcode','status' => $listing->backlinkchecker_code]),
            ])
            ->add('linkrelation', 'custom', [
                'label' => __('admin.backlinks.form.label.link_relation'), 
                'value' => view('misc/status', ['type' => 'linkrelation','status' => $listing->backlinkchecker_linkrelation]),
            ])
            ->add('retry', 'ro', ['label' => __('admin.backlinks.form.label.retry')])
            ->add('backlinkchecker_datetime', 'ro', [
                'label' => __('admin.backlinks.form.label.backlinkchecker_datetime'),
                'value' => locale()->formatDatetime($listing->backlinkchecker_datetime, auth()->user()->timezone),
            ])
            ->add('website', 'url', ['label' => __('admin.backlinks.form.label.website'), 'constraints' => 'required|backlink'])
            ->add('submit', 'submit', ['label' => __('admin.backlinks.form.label.update')])
            ->setValues([
                'retry' => $listing->backlinkchecker_retry,
                'website' => (null !== $field ? $field->get('value') : ''),
            ])
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                $listing->updated_datetime = date("Y-m-d H:i:s");

                db()->table('listingfielddata')
                    ->where('listing_id', $listing->id)
                    ->where('field_name', 'website')
                    ->update([
                        'value' => $input->get('website'),
                    ]);

                $listing->backlinkchecker_datetime = null;
                $listing->backlinkchecker_retry = null;
                $listing->backlinkchecker_linkrelation = null;
                $listing->save();

                return redirect(adminRoute($type->slug . '-invalid-backlinks', session()->get('admin/' . $type->slug . '-invalid-backlinks')))
                    ->with('success', view('flash/success', ['message' => __('admin.backlinks.alert.update.success', ['title' => $listing->title, 'id' => $listing->id, 'singular' => $type->name_singular, 'plural' => $type->name_plural])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/invalid-backlinks/update', [
                'type' => $type,
                'listing' => $listing,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        )); 
    }

    private function getTable($listings, $type)
    {
        return dataTable($listings)
            ->addColumns([
                'title' => [__('admin.backlinks.datatable.label.listing_title'), function ($listing) {
                    return $listing->title . ' (id:' . $listing->id . ')';
                }],
                'backlinkchecker_code' => [__('admin.backlinks.datatable.label.response_code'), function ($listing) {
                    return view('misc/status', [
                        'type' => 'httpcode',
                        'status' => $listing->backlinkchecker_code,
                    ]);
                }],
                'backlinkchecker_linkrelation' => [__('admin.backlinks.datatable.label.link_relation'), function ($listing) {
                    return view('misc/status', [
                        'type' => 'linkrelation',
                        'status' => $listing->backlinkchecker_linkrelation,
                    ]);
                }],
                'backlinkchecker_retry' => [__('admin.backlinks.datatable.label.retry')],
                'backlinkchecker_datetime' => [__('admin.backlinks.datatable.label.backlinkchecker_datetime'), function ($listing) {
                    return locale()->formatDatetimeDiff($listing->backlinkchecker_datetime);
                }],
            ])
            ->orderColumns([
                'title',
                'backlinkchecker_retry',
                'backlinkchecker_datetime',
            ])
            ->addActions([
                'edit' => [__('admin.backlinks.datatable.action.edit'), function ($listing) use ($type) {
                    return adminRoute($type->slug . '-invalid-backlinks/update/' . $listing->id);
                }],
                'summary' => [__('admin.backlinks.datatable.action.summary'), function ($listing) use ($type) {
                    return adminRoute('manage/' . $type->slug . '/summary/' . $listing->id);
                }],
            ]);
    }

}
