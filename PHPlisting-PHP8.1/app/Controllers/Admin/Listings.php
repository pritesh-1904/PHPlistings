<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Listings
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

        layout()->setTitle(__('admin.listings.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        if (null !== request()->post->get('action') && null !== request()->post->get('id')) {
            switch (request()->post->action) {
                case 'approve':
                    $listings = \App\Models\Listing::whereIn('id', (array) request()->post->get('id'))->whereNull('active')->get();

                    foreach ($listings as $listing) {
                        $listing->approve()->save();
                    }

                    $alert = view('flash/success', ['message' => __('admin.listings.alert.multiple_approve.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
                case 'delete':
                    $listings = \App\Models\Listing::whereIn('id', (array) request()->post->get('id'))->get();

                    foreach ($listings as $listing) {
                        $listing->delete();

                        if (null !== $type->get('active')) {
                            (new \App\Repositories\EmailQueue())->push(
                                'user_listing_removed',
                                $listing->user->id,
                                [
                                    'id' => $listing->user->id,
                                    'first_name' => $listing->user->first_name,
                                    'last_name' => $listing->user->last_name,
                                    'email' => $listing->user->email,

                                    'listing_id' => $listing->id,
                                    'listing_title' => $listing->title,
                                    'listing_type_singular' => $type->name_singular,
                                    'listing_type_plural' => $type->name_plural,
                                ],
                                [$listing->user->email => $listing->user->getName()],
                                [config()->email->from_email => config()->email->from_name]
                            );
                        }
                    }

                    $alert = view('flash/success', ['message' => __('admin.listings.alert.multiple_remove.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
            }
        }

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'asc';
        }

        if ('' !== request()->get->get('id', '')) {
            request()->get->put('slug', '');
            request()->get->put('keyword', '');
            request()->get->put('category_id', '');
            request()->get->put('location_id', '');
            request()->get->put('pricing_id', '');
            request()->get->put('user_id', '');
            request()->get->put('order_status', '');
        }

        $query = \App\Models\Listing::search(
                (new \App\Models\Listing(['type_id' => $type->id]))
                    ->setSearchable('id', 'eq')
                    ->setSearchable('slug', 'eq')
                    ->setSearchable('user_id', 'eq')
                    ->setSortable('end_datetime', 'end_datetime', null),
                [],
                'admin/manage/' . $type->slug
            )
            ->select(db()->raw('(SELECT orders.end_datetime FROM ' . db()->getPrefix() . 'orders AS orders WHERE orders.listing_id = ' . db()->getPrefix() . 'listings.id) AS end_datetime'))
            ->select(db()->raw('(SELECT orderspricing.pricing_id FROM ' . db()->getPrefix() . 'orders AS orderspricing WHERE orderspricing.listing_id = ' . db()->getPrefix() . 'listings.id) AS pricing_id'))
            ->select(db()->raw('(SELECT ordersstatus.status FROM ' . db()->getPrefix() . 'orders AS ordersstatus WHERE ordersstatus.listing_id = ' . db()->getPrefix() . 'listings.id) AS order_status'))
            ->where('type_id', $type->id)
            ->with([
                'order.pricing.product',
                'user',
                'reviews',
                'messages',
            ]);

        if ('' != request()->get->get('pricing_id', '')) {
            $query->having('pricing_id = "' . (int) request()->get->get('pricing_id') . '"');
        }

        if ('' != request()->get->get('order_status', '') && false !== in_array(request()->get->get('order_status', ''), ['active', 'suspended', 'cancelled', 'pending'])) {
            $query->having('order_status = "' . request()->get->get('order_status') . '"');
        }

        $listings = $query->paginate();

        $pricings = \App\Models\Pricing::query()
            ->whereHas('product', function($query) use ($type) {
                $query->where('type_id', $type->id);
            })
            ->with('product', function($query) {
                $query->orderBy('weight');
            })
            ->orderBy('weight')
            ->get();

        $form = form()
            ->setMethod('get')
            ->setTemplate('form/inline')
            ->add('id', 'number', [
                'placeholder' => __('admin.listings.searchform.label.id'),
                'attributes' => [
                    'style' => 'width: 120px;',
                ],
                'weight' => 10
            ])
            ->add('slug', 'text', [
                'placeholder' => __('admin.listings.searchform.label.slug'),
                'attributes' => [
                    'style' => 'width: 120px;',
                ],
                'weight' => 20
            ])
            ->add('keyword', 'text', [
                'placeholder' => __('admin.listings.searchform.label.keyword'),
                'weight' => 30
            ])
            ->add('category_id', 'category', [
                'type_id' => $type->id,
                'placeholder' => __('admin.listings.searchform.label.category'),
                'weight' => 40
            ])            
            ->add('user_id', 'user', [
                'placeholder' => __('admin.listings.searchform.label.user'),
                'weight' => 60
            ])
            ->add('pricing_id', 'select', [
                'options' => ['' => __('admin.listings.searchform.label.pricing_id')] + $pricings->pluck(function ($pricing) { return $pricing->getNameWithProduct(); }, 'id')->all(),
                'weight' => 70
            ])
            ->add('order_status', 'select', [
                'options' => [
                    '' => __('admin.listings.searchform.label.order_status'),
                    'active' => __('status.label.active'),
                    'suspended' => __('status.label.suspended'),
                    'cancelled' => __('status.label.cancelled'),
                    'pending' => __('status.label.pending'),
                ],
                'weight' => 80
            ])
            ->add('submit', 'submit', [
                'label' => __('admin.listings.searchform.label.submit')
            ])
            ->add('reset', 'button', [
                'label' => __('admin.listings.searchform.label.reset'),
                'attributes' => ['href' => adminRoute('manage/' . $type->slug)]
            ]);

        if (null !== $type->localizable) {
            $form
                ->add('location_id', 'location', [
                    'placeholder' => __('admin.listings.searchform.label.location'),
                    'weight' => 50
                ]);
        }

        $form->forceRequest();

        return response(layout()->content(
            view('admin/listings/index', [
                'type' => $type,
                'form' => $form,
                'listings' => $this->getTable($listings, $type),
                'alert' => $alert ?? null,
            ])
        ));
    }

    public function actionSummary($params)
    {
        if (false === auth()->check(['admin_content', 'admin_listings'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $listing = \App\Models\Listing::where('id', $params['id'])->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.listings.title.summary', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $upgrades = (new \App\Models\Product())->getTreeWithHiddenPricing($type->id, $listing->category_id);
        
        $form = form()
            ->add('pricing_id', 'tree', [
                'label' => __('admin.listings.form.label.current_pricing'),
                'value' => $listing->order->pricing_id,
                'tree_source' => $upgrades,
                'constraints' => 'required|maxlength:1'
            ])
            ->add('refund', 'toggle', ['label' => __('admin.listings.form.label.refund'), 'value' => 1])
            ->add('notification', 'toggle', ['label' => __('admin.listings.form.label.notification'), 'value' => 1])
            ->add('submit_change', 'submit', ['label' => __('admin.listings.form.label.change_pricing')])
            ->add('submit_cancel', 'submit', ['label' => __('admin.listings.form.label.cancel_pricing')]);

        if ('cancelled' == $listing->status) {
            $form->remove('submit_cancel');
        }
        
        $form->handleRequest(['submit_change', 'submit_cancel']);

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if (null !== request()->post->get('submit_change')) {
                if (count($upgrades) < 1 || null === $listing->order->pricing) {
                    $form->setValidationError('pricing_id', __('admin.listings.alert.upgrade_not_allowed'));
                }

                if (isset($input['pricing_id'][0])) {
                    if (null === \App\Models\Pricing::find($input['pricing_id'][0])) {
                        $form->setValidationError('pricing_id', __('admin.listings.alert.pricing_not_found'));
                    }
                }
            }

            if ($form->isValid()) {
                $emailData = [
                    'id' => $listing->user->id,
                    'first_name' => $listing->user->first_name,
                    'last_name' => $listing->user->last_name,
                    'email' => $listing->user->email,

                    'listing_id' => $listing->id,
                    'listing_title' => $listing->title,
                    'listing_product' => $listing->order->pricing->getNameWithProduct(),
                    'listing_type_singular' => $type->name_singular,
                    'listing_type_plural' => $type->name_plural,

                    'link' => route('account/manage/' . $listing->type->slug . '/summary/' . $listing->slug),
                ];

                if (null !== request()->post->get('submit_change')) {
                    if ($input['pricing_id'][0] != $listing->order->pricing_id || 'cancelled' == $listing->status) {
                        $listing->order->activate($input['pricing_id'][0], (null === $input->get('refund') ? false : true), (null === $input->get('notification') ? false : true));

                        $emailData['listing_new_product'] = \App\Models\Pricing::find($input['pricing_id'][0])->getNameWithProduct();

                        if (null !== $type->get('active') && null !== $input->get('notification')) {
                            (new \App\Repositories\EmailQueue())->push(
                                'user_order_changed',
                                $listing->user->id,
                                $emailData,
                                [$listing->user->email => $listing->user->getName()],
                                [config()->email->from_email => config()->email->from_name]
                            );
                        }
                    }
                } else if (null !== request()->post->get('submit_cancel')) {
                    $listing->order->deactivate((null === $input->get('refund') ? false : true));

                    if (null !== $type->get('active') && null !== $input->get('notification')) {
                        (new \App\Repositories\EmailQueue())->push(
                            'user_order_cancelled',
                            $listing->user->id,
                            $emailData,
                            [$listing->user->email => $listing->user->getName()],
                            [config()->email->from_email => config()->email->from_name]
                        );
                    }
                }

                return redirect(adminRoute('manage/' . $type->slug, session()->get('admin/manage/' . $type->slug)))
                    ->with('success', view('flash/success', ['message' => __('admin.listings.alert.update.success', ['title' => $listing->title, 'id' => $listing->id, 'singular' => $type->name_singular, 'plural' => $type->name_plural])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }
        
        return layout()->content(
            view('admin/listings/summary', [
                'type' => $type,
                'listing' => $listing,
                'form' => $form,
                'alert' => $alert ?? '',
            ])
        );        
    }

    public function actionApprove($params)
    {
        if (false === auth()->check(['admin_content', 'admin_listings'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.listings.title.approve', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));
        
        if (null !== request()->post->get('action') && null !== request()->post->get('id')) {
            switch (request()->post->action) {
                case 'approve':
                    $listings = \App\Models\Listing::whereIn('id', (array) request()->post->get('id'))->whereNull('active')->get();

                    foreach ($listings as $listing) {
                        $listing->approve()->save();
                    }

                    $alert = view('flash/success', ['message' => __('admin.listings.alert.multiple_approve.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
                case 'delete':
                    $listings = \App\Models\Listing::whereIn('id', (array) request()->post->get('id'))->get();

                    foreach ($listings as $listing) {
                        $listing->delete();

                        if (null !== $type->get('active')) {
                            (new \App\Repositories\EmailQueue())->push(
                                'user_listing_removed',
                                $listing->user->id,
                                [
                                    'id' => $listing->user->id,
                                    'first_name' => $listing->user->first_name,
                                    'last_name' => $listing->user->last_name,
                                    'email' => $listing->user->email,

                                    'listing_id' => $listing->id,
                                    'listing_title' => $listing->title,
                                    'listing_type_singular' => $type->name_singular,
                                    'listing_type_plural' => $type->name_plural,
                                ],
                                [$listing->user->email => $listing->user->getName()],
                                [config()->email->from_email => config()->email->from_name]
                            );
                        }
                    }

                    $alert = view('flash/success', ['message' => __('admin.listings.alert.multiple_remove.success', ['singular' => $type->name_singular, 'plural' => $type->name_plural])]);
                    break;
            }
        }

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'asc';
        }

        $listings = \App\Models\Listing::search(
                (new \App\Models\Listing(['type_id' => $type->id]))
                    ->setSearchable('user_id', 'eq'),
                [],
                'admin/manage/' . $type->slug . '/approve'
            )
            ->where('type_id', $type->id)
            ->whereNull('active')
            ->with([
                'order.pricing.product',
                'user',
                'reviews',
                'messages',
            ])
            ->paginate();

        return response(layout()->content(
            view('admin/listings/approve', [
                'type' => $type,
                'listings' => $this->getTable($listings, $type, true),
                'alert' => $alert ?? null,
            ])
        ));
    }

    public function actionApproveUpdates($params)
    {
        if (false === auth()->check(['admin_content', 'admin_listings'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.listings.title.approve_updates', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $updates = \App\Models\Update::search(
                (new \App\Models\Update(['type_id' => $type->id])),
                [],
                'admin/manage/' . $type->slug . '/approve-updates'
            )
            ->where('type_id', $type->id)
            ->with([
                'listing',
                'listing.order.pricing.product',
                'listing.user',
            ])
            ->paginate();

        $table = dataTable($updates)
            ->addColumns([
                'id' => [__('admin.listings.datatable.label.id')],
                'title' => [__('admin.listings.datatable.label.title'), function ($update) {
                    return $update->listing->title;
                }],
                'product' => [__('admin.listings.datatable.label.product'), function ($update) {
                    return $update->listing->order->pricing->getNameWithProduct();
                }],
                'status' => [__('admin.listings.datatable.label.status'), function ($update) {
                    return view('misc/status', [
                        'type' => 'order',
                        'status' => $update->listing->status,
                    ]);
                }],
                'end_datetime' => [__('admin.listings.datatable.label.end_datetime'), function ($update) {
                    return locale()->formatDatetime($update->listing->order->end_datetime, auth()->user()->timezone);
                }],
            ])
            ->orderColumns([
                'id',
                'title',
                'active',
            ])
            ->addActions([
                'edit' => [__('admin.listings.datatable.action.edit_approve'), function ($update) use ($type) {
                    return adminRoute('manage/' . $type->slug . '/approve-update/' . $update->id);
                }],
                'delete' => [__('admin.listings.datatable.action.reject'), function ($update) use ($type) {
                    return adminRoute('manage/' . $type->slug . '/reject-update/' . $update->id);
                }],
            ]);

        return response(layout()->content(
            view('admin/listings/approve-updates', [
                'type' => $type,
                'updates' => $table,
            ])
        ));
    }

    public function actionApproveUpdate($params)
    {
        if (false === auth()->check(['admin_content', 'admin_listings'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $update = \App\Models\Update::where('id', $params['id'])->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.listings.title.approve_update', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $listing = $update->export();

        $form = form()
            ->add('category_id', 'tree', [
                'label' => __('admin.listings.form.label.primary_category'),
                'tree_source' => (new \App\Models\Category())->getTree($type->id, $listing->order->pricing_id),
                'constraints' => 'required|maxlength:1',
            ]);

        if ($listing->_extra_categories > 0) {
            $form
                ->add('categories', 'tree', [
                    'label' => __('admin.listings.form.label.extra_categories'), 
                    'tree_source' => (new \App\Models\Category())->getTree($type->id, $listing->order->pricing_id, true),
                    'constraints' => 'maxlength:' . $listing->_extra_categories,
                ])
                ->setValue('categories', $update->categories->pluck('id')->all());
        }

        if ($type->parents->count() > 0) {
            $parents = $update->parents()->get();

            foreach ($type->parents as $parent) {
                $form
                    ->add('parent_' . $parent->id, 'listing', [
                        'label' => __('admin.listings.form.label.parent', ['singular' => $parent->name_singular, 'plural' => $parent->name_plural]),
                        'type' => $type->id,
                        'constraints' => 'listing:' . $parent->id,
                    ])
/*
                    ->add('parent_' . $parent->id, 'tree', [
                        'label' => __('admin.listings.form.label.parent', ['singular' => $parent->name_singular, 'plural' => $parent->name_plural]),
                        'tree_source' => (new \App\Models\Listing())->getTree($parent->id),
                    ])
*/
                    ->setValue('parent_' . $parent->id, (null !== $parents->where('type_id', $parent->id)->first() ? $parents->where('type_id', $parent->id)->first()->id : null));
            }
        }

        $form->bindModel($listing, 'update', ['pricing_id' => $listing->order->pricing_id]);

        $form->get('title')->addConstraint('maxlength:' . $listing->_title_size);

        if (null !== $listing->get('_backlink')) {
            if (null !== $form->get('website')) {
                $form->get('website')
                    ->addConstraint('required')
                    ->addConstraint('backlink');
            }
        }

        if ($listing->get('_short_description_size') < 1) {
            $form->remove('short_description');
        } else {
            $form->get('short_description')->addConstraint('maxlength:' . $listing->_short_description_size);
        }

        $form->get('description')->addConstraint('htmlmaxtags:a,' . $listing->_description_links_limit);

        if ($listing->get('_description_size') < 1) {
            $form->remove('description');
        } else {
            $form->get('description')->addConstraint('htmlmaxlength:' . $listing->_description_size);
        }

        $form
            ->add('submit', 'submit', ['label' => __('admin.listings.form.label.approve')])
            ->add('reject', 'button', ['label' => __('admin.listings.form.label.reject'), 'attributes' => ['href' => adminRoute('manage/' . $type->slug . '/reject-update/' . $update->id)]])
            ->setValues($update->data->pluck('value', 'field_name')->all())
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                if (\App\Models\Listing::where('slug', $form->getValues()->slug)->where('type_id', $type->id)->where('id', '!=', $listing->id)->count() > 0) {
                    $form->setValidationError('slug', __('form.validation.unique'));
                }

                if ('Event' == $type->type) {
                    $dates = $listing->getEventDates();

                    if (count($dates) == 0) {
                        $form->setValidationError('event_start_datetime', __('admin.listings.alert.event_no_dates'));
                    }

                    if (strtotime($input->get('event_start_datetime')) > strtotime($input->get('event_end_datetime'))) {
                        $form->setValidationError('event_end_datetime', __('admin.listings.alert.event_start_after_end'));
                    }

                    if (count($dates) > $listing->get('_event_dates')) {
                        $form->setValidationError('event_end_datetime', __('admin.listings.form.alert.dates_limit_reached', ['limit' => $listing->get('_event_dates')]));
                    }

                    if ('custom' == $input->get('event_frequency') && '' == $input->get('event_dates', '')) {
                        $form->setValidationError('event_dates', __('form.validation.required'));
                    }
                }

                if ('Offer' == $type->type) {
                    if (strtotime($input->get('offer_start_datetime')) > strtotime($input->get('offer_end_datetime'))) {
                        $form->setValidationError('offer_end_datetime', __('admin.listings.alert.offer_start_after_end'));
                    }

                    if ('percentage' == $input->get('offer_discount_type') && ($input->get('offer_discount') < 0 || $input->get('offer_discount') > 100)) {
                        $form->setValidationError('offer_discount', __('admin.listings.alert.offer_invalid_percentage'));
                    }
                }
            }

            if ($form->isValid()) {
                $listing->updated_datetime = date("Y-m-d H:i:s");

                $listing->category_id = $input->category_id[0];

                $listing->deadlinkchecker_datetime = null;
                $listing->deadlinkchecker_retry = null;

                $listing->backlinkchecker_datetime = null;
                $listing->backlinkchecker_retry = null;

                $listing->saveWithData($input);

                if ('Event' == $type->type) {
                    $listing->saveEventDates($dates);
                }

                $listing->categories()->sync($input->get('categories') ?? []);

                $parents = [];

                foreach ($type->parents as $parent) {
//                    $parents = array_merge($parents, $input->get('parent_' . $parent->id, []));
                    if (null !== $input->get('parent_' . $parent->id)) {
                        $parents[] = $input->get('parent_' . $parent->id);
                    }
                }

                $listing->parents()->sync($parents);

                $update->delete();

                if (null !== $type->get('active')) {
                    (new \App\Repositories\EmailQueue())->push(
                        'user_listing_update_approved',
                        $listing->user->id,
                        [
                            'id' => $listing->user->id,
                            'first_name' => $listing->user->first_name,
                            'last_name' => $listing->user->last_name,
                            'email' => $listing->user->email,

                            'listing_id' => $listing->id,
                            'listing_title' => $listing->title,
                            'listing_type_singular' => $type->name_singular,
                            'listing_type_plural' => $type->name_plural,
                        ],
                        [$listing->user->email => $listing->user->getName()],
                        [config()->email->from_email => config()->email->from_name]
                    );
                }

                return redirect(adminRoute('manage/' . $type->slug . '/approve-updates', session()->get('admin/manage/' . $type->slug . '/approve-updates')))
                    ->with('success', view('flash/success', ['message' => __('admin.listings.alert.approve_update.success', ['title' => $listing->title, 'id' => $listing->id, 'singular' => $type->name_singular, 'plural' => $type->name_plural])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/listings/approve-update', [
                'type' => $type,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        )); 
    }

    public function actionRejectUpdate($params)
    {
        if (false === auth()->check(['admin_content', 'admin_listings'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $update = \App\Models\Update::where('id', $params['id'])->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $update->delete();

        if (null !== $type->get('active')) {
            (new \App\Repositories\EmailQueue())->push(
                'user_listing_update_rejected',
                $update->listing->user->id,
                [
                    'id' => $update->listing->user->id,
                    'first_name' => $update->listing->user->first_name,
                    'last_name' => $update->listing->user->last_name,
                    'email' => $update->listing->user->email,

                    'listing_id' => $update->listing->id,
                    'listing_title' => $update->listing->title,
                    'listing_type_singular' => $type->name_singular,
                    'listing_type_plural' => $type->name_plural,
                ],
                [$update->listing->user->email => $update->listing->user->getName()],
                [config()->email->from_email => config()->email->from_name]
            );
        }

        return redirect(adminRoute('manage/' . $type->slug . '/approve-updates', session()->get('admin/manage/' . $type->slug . '/approve-updates')))
            ->with('success', view('flash/success', ['message' => __('admin.listings.alert.reject_update.success', ['title' => $update->listing->title, 'id' => $update->listing->id, 'singular' => $type->name_singular, 'plural' => $type->name_plural])]));
    }

    public function actionCreate($params)
    {
        if (false === auth()->check(['admin_content', 'admin_listings'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.listings.title.create', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        if (null === request()->get->get('pricing_id')) {
            $form = form()
                ->add('pricing_id', 'tree', [
                    'label' => __('admin.listings.form.label.pricing'), 
                    'tree_source' => (new \App\Models\Product())->getTreeWithHiddenPricing($type->id),
                    'constraints' => 'required|maxlength:1'
                ])
                ->add('submit_pricing', 'submit', ['label' => __('admin.listings.form.label.submit_pricing')])
                ->handleRequest('submit_pricing');

            if ($form->isSubmitted()) {
                $input = $form->getValues();

                if ($form->isValid()) {
                    return redirect(adminRoute('manage/' . $type->slug . '/create', ['pricing_id' => $input['pricing_id'][0]]));
                } else {
                    $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
                }
            }
        } else if (null === request()->get->get('category_id')) {
            $form = form()
                ->add('category_id', 'tree', [
                    'label' => __('admin.listings.form.label.primary_category'), 
                    'tree_source' => (new \App\Models\Category())->getTree($type->id, request()->get->get('pricing_id')),
                    'constraints' => 'required|maxlength:1'
                ])
                ->add('submit_category', 'submit', ['label' => __('admin.listings.form.label.submit_category')])
                ->handleRequest('submit_category');

            if ($form->isSubmitted()) {
                $input = $form->getValues();

                if ($form->isValid()) {
                    return redirect(adminRoute('manage/' . $type->slug . '/create', ['pricing_id' => request()->get->pricing_id, 'category_id' => $input['category_id'][0]]));
                } else {
                    $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
                }
            }
        } else {
            if (null === $pricing = \App\Models\Pricing::where('id', request()->get->get('pricing_id'))->first()) {
                return redirect(adminRoute('manage/' . $type->slug . '/create'))
                    ->with('error', view('flash/error', ['message' => __('admin.listings.alert.pricing_not_found')]));
            }

            $listing = new \App\Models\Listing();
            $listing->type_id = $type->id;
            $listing->category_id = request()->get->get('category_id');

            $order = new \App\Models\Order();
            $order->pricing_id = $pricing->id;

            $form = form()
                ->add('active', 'toggle', ['label' => __('admin.listings.form.label.approved'), 'value' => ((null !== $type->approvable && null === $pricing->autoapprovable) ? null : '1')])
                ->add('claimed', 'toggle', ['label' => __('admin.listings.form.label.claimed'), 'value' => 1])
                ->add('user_id', 'user', ['label' => __('admin.listings.form.label.user'), 'placeholder' => __('admin.listings.form.placeholder.user'), 'constraints' => 'required'])
                ->add('category', 'ro', [
                    'label' => __('admin.listings.form.label.primary_category'),
                    'value' => $listing->getOutputableValue('_category'),
                ]);

            if ($pricing->product->_extra_categories > 0) {
                $form
                    ->add('categories', 'tree', [
                        'label' => __('admin.listings.form.label.extra_categories'), 
                        'tree_source' => (new \App\Models\Category())->getTree($type->id, $pricing->id, true, request()->get->get('category_id')),
                        'constraints' => 'maxlength:' . $pricing->product->_extra_categories
                    ]);
            }

            if ($type->parents->count() > 0) {
                foreach ($type->parents as $parent) {
                    $form
                        ->add('parent_' . $parent->id, 'listing', [
                            'label' => __('admin.listings.form.label.parent', ['singular' => $parent->name_singular, 'plural' => $parent->name_plural]),
                            'type' => $parent->id,
                            'constraints' => 'listing:' . $parent->id,
                        ]);
/*
                        ->add('parent_' . $parent->id, 'tree', [
                            'label' => __('admin.listings.form.label.parent', ['singular' => $parent->name_singular, 'plural' => $parent->name_plural]),
                            'tree_source' => (new \App\Models\Listing())->getTree($parent->id),
                        ]);
*/
                }
            }

            if ($type->badges()->count() > 0) {
                $form
                    ->add('badges', 'badges', [
                        'label' => __('admin.listings.form.label.badges'),
                        'type_id' => $type->id,
                        'weight' => 1000
                    ]);
            }

            $form
                ->bindModel($listing, 'submit', ['pricing_id' => $pricing->id])
                ->add('submit', 'submit', ['label' => __('admin.listings.form.label.submit')]);

            $form->get('title')->addConstraint('maxlength:' . $pricing->product->_title_size);

            if (null !== $pricing->product->get('_backlink')) {
                $form->get('website')
                    ->addConstraint('required')
                    ->addConstraint('backlink');
            }

            if ($pricing->product->get('_short_description_size') < 1) {
                $form->remove('short_description');
            } else {
                $form->get('short_description')->addConstraint('maxlength:' . $pricing->product->get('_short_description_size'));
            }

            $form->get('description')->addConstraint('htmlmaxtags:a,' . $pricing->product->get('_description_links_limit'));

            if ($pricing->product->get('_description_size') < 1) {
                $form->remove('description');
            } else {
                $form->get('description')->addConstraint('htmlmaxlength:' . $pricing->product->get('_description_size'));
            }

            $form->handleRequest();

            if ($form->isSubmitted()) {
                $input = $form->getValues();

                if ($form->isValid()) {
                    if (\App\Models\Listing::where('slug', $input->get('slug'))->where('type_id', $type->id)->count() > 0) {
                        $form->setValidationError('slug', __('form.validation.unique'));
                    }

                    if ('Event' == $type->type) {
                        $dates = $listing->getEventDates();

                        if (count($dates) == 0) {
                            $form->setValidationError('event_start_datetime', __('admin.listings.alert.event_no_dates'));
                        }

                        if (strtotime($input->get('event_start_datetime')) > strtotime($input->get('event_end_datetime'))) {
                            $form->setValidationError('event_end_datetime', __('admin.listings.alert.event_start_after_end'));
                        }

                        if (count($dates) > $pricing->product->_event_dates) {
                            $form->setValidationError('event_end_datetime', __('admin.listings.form.alert.dates_limit_reached', ['limit' => $pricing->product->_event_dates]));
                        }

                        if ('custom' == $input->get('event_frequency') && '' == $input->get('event_dates', '')) {
                            $form->setValidationError('event_dates', __('form.validation.required'));
                        }
                    }

                    if ('Offer' == $type->type) {
                        if (strtotime($input->get('offer_start_datetime')) > strtotime($input->get('offer_end_datetime'))) {
                            $form->setValidationError('offer_end_datetime', __('admin.listings.alert.offer_start_after_end'));
                        }

                        if ('percentage' == $input->get('offer_discount_type') && ($input->get('offer_discount') < 0 || $input->get('offer_discount') > 100)) {
                            $form->setValidationError('offer_discount', __('admin.listings.alert.offer_invalid_percentage'));
                        }
                    }
                }

                if ($form->isValid()) {
                    $listing->active = $input->active;
                    $listing->claimed = $input->claimed;
                    $listing->added_datetime = date("Y-m-d H:i:s");
                    $listing->user_id = $input->user_id;
                    $listing->rating = 0;
                    $listing->review_count = 0;

                    $order->user_id = $input->user_id;

                    $listing->saveWithData($input);

                    $listing->order()->save($order);

                    $listing->badges()->sync($input->get('badges') ?? []);
                    
                    $order->activate($pricing->id);

                    if ('Event' == $type->type) {
                        $listing->saveEventDates($dates);
                    }

                    $listing->categories()->sync($input->get('categories') ?? []);

                    $parents = [];

                    foreach ($type->parents as $parent) {
//                        $parents = array_merge($parents, $input->get('parent_' . $parent->id, []));

                        if (null !== $input->get('parent_' . $parent->id)) {
                            $parents[] = $input->get('parent_' . $parent->id);
                        }
                    }

                    $listing->parents()->attach($parents);

                    return redirect(adminRoute('manage/' . $type->slug, session()->get('admin/manage/' . $type->slug)))
                        ->with('success', view('flash/success', ['message' => __('admin.listings.alert.create.success', ['title' => $listing->title, 'id' => $listing->id, 'singular' => $type->name_singular, 'plural' => $type->name_plural])]));
                } else {
                    $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
                }
            }
        }

        return response(layout()->content(
            view('admin/listings/create', [
                'type' => $type,
                'form' => $form,
                'alert' => $alert ?? null
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

        if (null === $listing = \App\Models\Listing::where('id', $params['id'])->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.listings.title.update', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $form = form()
            ->add('active', 'toggle', ['label' => __('admin.listings.form.label.approved')])
            ->add('claimed', 'toggle', ['label' => __('admin.listings.form.label.claimed')])
            ->add('user_id', 'user', ['label' => __('admin.listings.form.label.user'), 'placeholder' => __('admin.listings.form.placeholder.user'), 'constraints' => 'required'])
            ->add('category_id', 'tree', [
                'label' => __('admin.listings.form.label.primary_category'),
                'tree_source' => (new \App\Models\Category())->getTree($type->id, $listing->order->pricing_id),
                'constraints' => 'required|maxlength:1',
            ]);

        if ($listing->_extra_categories > 0) {
            $form
                ->add('categories', 'tree', [
                    'label' => __('admin.listings.form.label.extra_categories'),
                    'tree_source' => (new \App\Models\Category())->getExpandedTree($type->id, $listing->order->pricing_id),
                    'constraints' => 'maxlength:' . $listing->_extra_categories,
                ])
                ->setValue('categories', $listing->categories->pluck('id')->all());
        }

        if ($type->parents->count() > 0) {            
            $parents = $listing->parents()->get();

            foreach ($type->parents as $parent) {
                $form
                    ->add('parent_' . $parent->id, 'listing', [
                        'label' => __('admin.listings.form.label.parent', ['singular' => $parent->name_singular, 'plural' => $parent->name_plural]),
                        'type' => $parent->id,
                        'constraints' => 'listing:' . $parent->id,
                    ])
/*
                    ->add('parent_' . $parent->id, 'tree', [
                        'label' => __('admin.listings.form.label.parent', ['singular' => $parent->name_singular, 'plural' => $parent->name_plural]),
                        'tree_source' => (new \App\Models\Listing())->getTree($parent->id),
                    ])
*/
                    ->setValue('parent_' . $parent->id, (null !== $parents->where('type_id', $parent->id)->first() ? $parents->where('type_id', $parent->id)->first()->id : null));
            }
        }

        if ($type->badges()->count() > 0) {
            $form
                ->add('badges', 'badges', [
                    'label' => __('admin.listings.form.label.badges'),
                    'type_id' => $type->id,
                    'weight' => 1000
                ])
                ->setValue('badges', $listing->badges->pluck('id')->all());
        }

        $form->bindModel($listing, 'update', ['pricing_id' => $listing->order->pricing_id]);

        $form->get('title')->addConstraint('maxlength:' . $listing->_title_size);

        if (null !== $listing->get('_backlink')) {
            $form->get('website')
                ->addConstraint('required')
                ->addConstraint('backlink');
        }

        if ($listing->_short_description_size < 1) {
            $form->remove('short_description');
        } else {
            $form->get('short_description')->addConstraint('maxlength:' . $listing->_short_description_size);
        }

        $form->get('description')->addConstraint('htmlmaxtags:a,' . $listing->_description_links_limit);

        if ($listing->_description_size < 1) {
            $form->remove('description');
        } else {
            $form->get('description')->addConstraint('htmlmaxlength:' . $listing->_description_size);
        }

        $form
            ->add('submit', 'submit', ['label' => __('admin.listings.form.label.update')])
            ->setValues($listing->data->pluck('value', 'field_name')->all())
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                if (\App\Models\Listing::where('id', '!=', $listing->id)->where('slug', $input->get('slug'))->where('type_id', $type->id)->count() > 0) {
                    $form->setValidationError('slug', __('form.validation.unique'));
                }

                if ('Event' == $type->type) {
                    $dates = $listing->getEventDates();

                    if (count($dates) == 0) {
                        $form->setValidationError('event_start_datetime', __('admin.listings.alert.event_no_dates'));
                    }

                    if (strtotime($input->get('event_start_datetime')) > strtotime($input->get('event_end_datetime'))) {
                        $form->setValidationError('event_end_datetime', __('admin.listings.alert.event_start_after_end'));
                    }

                    if (count($dates) > $listing->order->pricing->product->get('_event_dates')) {
                        $form->setValidationError('event_end_datetime', __('admin.listings.form.alert.dates_limit_reached', ['limit' => $listing->order->pricing->product->get('_event_dates')]));
                    }

                    if ('custom' == $input->get('event_frequency') && '' == $input->get('event_dates', '')) {
                        $form->setValidationError('event_dates', __('form.validation.required'));
                    }
                }

                if ('Offer' == $type->type) {
                    if (strtotime($input->get('offer_start_datetime')) > strtotime($input->get('offer_end_datetime'))) {
                        $form->setValidationError('offer_end_datetime', __('admin.listings.alert.offer_start_after_end'));
                    }

                    if ('percentage' == $input->get('offer_discount_type') && ($input->get('offer_discount') < 0 || $input->get('offer_discount') > 100)) {
                        $form->setValidationError('offer_discount', __('admin.listings.alert.offer_invalid_percentage'));
                    }
                }
            }

            if ($form->isValid()) {
                $listing->updated_datetime = date("Y-m-d H:i:s");

                $listing->deadlinkchecker_datetime = null;
                $listing->deadlinkchecker_retry = null;

                $listing->backlinkchecker_datetime = null;
                $listing->backlinkchecker_retry = null;

                if ($listing->active != $input->active) {
                    if (null !== $input->active) {
                        $listing->approve();
                    } else {
                        $listing->disapprove();
                    }
                }
                
                $listing->claimed = $input->get('claimed');
                $listing->category_id = $input->category_id[0];

                if ($listing->user_id != $input->user_id) {
                    $listing->changeUser($input->user_id);
                }
                
                $listing->saveWithData($input);

                if ('Event' == $type->type) {
                    $listing->saveEventDates($dates);
                }

                $listing->categories()->sync($input->categories ?? []);

                $parents = [];

                foreach ($type->parents as $parent) {
//                    $parents = array_merge($parents, $input->get('parent_' . $parent->id, []));

                    if (null !== $input->get('parent_' . $parent->id)) {
                        $parents[] = $input->get('parent_' . $parent->id);
                    }
                }

                $listing->parents()->sync($parents);

                $productBadges = $listing->badges()->wherePivot('product', 'isnotnull', true)->get();
                
                $listing->badges()->detach();

                foreach ($input->get('badges') ?? [] as $id) {
                    $listing->badges()->attach($id, ['product' => (false !== $productBadges->contains('id', $id)) ? 1 : null]);
                }
                
                if (null !== request()->get->get('approval')) {
                    return redirect(adminRoute('manage/' . $type->slug . '/approve', session()->get('admin/manage/' . $type->slug . '/approve')))
                        ->with('success', view('flash/success', ['message' => __('admin.listings.alert.update.success', ['title' => $listing->title, 'id' => $listing->id, 'singular' => $type->name_singular, 'plural' => $type->name_plural])]));
                } else {
                    return redirect(adminRoute('manage/' . $type->slug, session()->get('admin/manage/' . $type->slug)))
                        ->with('success', view('flash/success', ['message' => __('admin.listings.alert.update.success', ['title' => $listing->title, 'id' => $listing->id, 'singular' => $type->name_singular, 'plural' => $type->name_plural])]));
                }
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/listings/update', [
                'type' => $type,
                'listing' => $listing,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        )); 
    }

    public function actionDelete($params)
    {
        if (false === auth()->check(['admin_content', 'admin_listings'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $listing = \App\Models\Listing::where('id', $params['id'])->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $listing->delete();

        if (null !== $type->get('active')) {
            (new \App\Repositories\EmailQueue())->push(
                'user_listing_removed',
                $listing->user->id,
                [
                    'id' => $listing->user->id,
                    'first_name' => $listing->user->first_name,
                    'last_name' => $listing->user->last_name,
                    'email' => $listing->user->email,

                    'listing_id' => $listing->id,
                    'listing_title' => $listing->title,
                    'listing_type_singular' => $type->name_singular,
                    'listing_type_plural' => $type->name_plural,
                ],
                [$listing->user->email => $listing->user->getName()],
                [config()->email->from_email => config()->email->from_name]
            );
        }

        return redirect(adminRoute('manage/' . $type->slug, session()->get('admin/manage/' . $type->slug)))
            ->with('success', view('flash/success', ['message' => __('admin.listings.alert.remove.success', ['title' => $listing->title, 'id' => $listing->id, 'singular' => $type->name_singular, 'plural' => $type->name_plural])]));
    }

    private function getTable($listings, $type, $approval = false)
    {
        return dataTable($listings)
            ->addColumns([
                'id' => [__('admin.listings.datatable.label.id')],
                'title' => [__('admin.listings.datatable.label.title')],
                'product' => [__('admin.listings.datatable.label.product'), function ($listing) {
                    return $listing->order->pricing->getNameWithProduct();
                }],
                'order_status' => [__('admin.listings.datatable.label.status'), function ($listing) {
                    return view('misc/status', [
                        'type' => 'order',
                        'status' => $listing->status,
                    ]);
                }],
                'end_datetime' => [__('admin.listings.datatable.label.end_datetime'), function ($listing) {
                    return locale()->formatDatetime($listing->order->end_datetime, auth()->user()->timezone);
                }],
                'active' => [__('admin.listings.datatable.label.approved'), function ($listing) {
                    return view('misc/ajax-switch', [
                        'table' => 'listings',
                        'column' => 'active',
                        'id' => $listing->id,
                        'value' => $listing->active
                    ]);
                }],                        
            ])
            ->orderColumns([
                'id',
                'title',
                'active',
                'end_datetime',
            ])
            ->addActions([
                'preview' => [__('admin.listings.datatable.action.preview'), function ($listing) use ($type) {
                    if (null !== $listing->get('_page')) {
                        return route($type->slug . '/' . $listing->slug);
                    }
                }, true],
                'summary' => [__('admin.listings.datatable.action.summary'), function ($listing) use ($type) {
                    return adminRoute('manage/' . $type->slug . '/summary/' . $listing->id);
                }],
                'edit' => [__('admin.listings.datatable.action.edit'), function ($listing) use ($type, $approval) {
                    return adminRoute('manage/' . $type->slug . '/update/' . $listing->id, ['approval' => (false !== $approval ? 'true' : null)]);
                }],
                'reviews' => [
                    function ($listing) {
                        return __('admin.listings.datatable.action.reviews', ['count' => '<span class="badge badge-secondary">' . $listing->reviews->count() . '</span>']);
                    },
                    function ($listing) use ($type) {
                        if (false === auth()->check(['admin_content', 'admin_reviews'])) {
                            return null;
                        }
                        
                        if ($type->reviewable == '1') {
                            return adminRoute($type->slug . '-reviews', ['listing_id' => $listing->id]);
                        }
                    }
                ],
                'messages' => [
                    function ($listing) {
                        return __('admin.listings.datatable.action.messages', ['count' => '<span class="badge badge-secondary">' . $listing->messages->count() . '</span>']);
                    },
                    function ($listing) use ($type) {
                        if (false === auth()->check(['admin_content', 'admin_messages'])) {
                            return null;
                        }

                        return adminRoute($type->slug . '-messages', ['listing_id' => $listing->id]);
                    }
                ],
                'delete' => [__('admin.listings.datatable.action.delete'), function ($listing) use ($type) {
                    return adminRoute('manage/' . $type->slug . '/delete/' . $listing->id);
                }],
            ])
            ->addBulkActions([
                'approve' => __('admin.listings.datatable.bulkaction.approve'),
                'delete' => __('admin.listings.datatable.bulkaction.delete'),
            ]);
    }

}
