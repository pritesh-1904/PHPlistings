<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Deadlinks
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

        layout()->setTitle(__('admin.deadlinks.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'deadlinkchecker_datetime';
            request()->get->sort_direction = 'desc';
        }

        $listings = \App\Models\Listing::search(
                (new \App\Models\Listing(['type_id' => $type->id]))
                    ->setSearchable('user_id', 'eq')
                    ->setSortable('deadlinkchecker_retry', 'deadlinkchecker_retry', null)
                    ->setSortable('deadlinkchecker_code', 'deadlinkchecker_code', null)
                    ->setSortable('deadlinkchecker_datetime', 'deadlinkchecker_datetime', null),
                [],
                'admin/' . $type->slug . '-broken-links'
            )
            ->where('type_id', $type->id)
            ->whereNotNull('deadlinkchecker_retry')
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
            view('admin/broken-links/index', [
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

        if (null === $listing = \App\Models\Listing::where('id', $params['id'])->where('type_id', $type->id)->whereNotNull('deadlinkchecker_retry')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.deadlinks.title.update', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $field = $listing->data->where('field_name', 'website')->first();

        $form = form()
            ->add('title', 'ro', [
                'label' => __('admin.deadlinks.form.label.listing_title'), 
                'value' => $listing->title . ' (' . $listing->id . ')',
            ])
            ->add('response_code', 'custom', [
                'label' => __('admin.deadlinks.form.label.response_code'), 
                'value' => view('misc/status', ['type' => 'httpcode','status' => $listing->deadlinkchecker_code]),
            ])
            ->add('retry', 'ro', ['label' => __('admin.deadlinks.form.label.retry')])
            ->add('deadlinkchecker_datetime', 'ro', [
                'label' => __('admin.deadlinks.form.label.deadlinkchecker_datetime'),
                'value' => locale()->formatDatetime($listing->deadlinkchecker_datetime, auth()->user()->timezone),
            ])
            ->add('website', 'url', ['label' => __('admin.deadlinks.form.label.website'), 'constraints' => 'required|url:advanced'])
            ->add('submit', 'submit', ['label' => __('admin.deadlinks.form.label.update')])
            ->setValues([
                'retry' => $listing->deadlinkchecker_retry,
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

                $listing->deadlinkchecker_datetime = null;
                $listing->deadlinkchecker_retry = null;
                $listing->save();

                return redirect(adminRoute($type->slug . '-broken-links', session()->get('admin/' . $type->slug . '-broken-links')))
                    ->with('success', view('flash/success', ['message' => __('admin.deadlinks.alert.update.success', ['title' => $listing->title, 'id' => $listing->id, 'singular' => $type->name_singular, 'plural' => $type->name_plural])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/broken-links/update', [
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
                'title' => [__('admin.deadlinks.datatable.label.listing_title'), function ($listing) {
                    return $listing->title . ' (id:' . $listing->id . ')';
                }],
                'deadlinkchecker_code' => [__('admin.deadlinks.datatable.label.response_code'), function ($listing) {
                    return view('misc/status', [
                        'type' => 'httpcode',
                        'status' => $listing->deadlinkchecker_code,
                    ]);
                }],
                'deadlinkchecker_retry' => [__('admin.deadlinks.datatable.label.retry')],
                'deadlinkchecker_datetime' => [__('admin.deadlinks.datatable.label.deadlinkchecker_datetime'), function ($listing) {
                    return locale()->formatDatetimeDiff($listing->deadlinkchecker_datetime);
                }],
            ])
            ->orderColumns([
                'title',
                'deadlinkchecker_retry',
                'deadlinkchecker_code',
                'deadlinkchecker_datetime',
            ])
            ->addActions([
                'edit' => [__('admin.deadlinks.datatable.action.edit'), function ($listing) use ($type) {
                    return adminRoute($type->slug . '-broken-links/update/' . $listing->id);
                }],
                'summary' => [__('admin.deadlinks.datatable.action.summary'), function ($listing) use ($type) {
                    return adminRoute('manage/' . $type->slug . '/summary/' . $listing->id);
                }],
            ]);
    }

}
