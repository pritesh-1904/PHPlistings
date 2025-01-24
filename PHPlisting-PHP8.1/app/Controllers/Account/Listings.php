<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Account;

class Listings
    extends \App\Controllers\Account\BaseController
{

    public function actionIndex($params)
    {
        if (null === $page = \App\Models\Page::where('slug', 'account/manage/type')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ((null !== config()->general->maintenance || null === $page->active) && false === auth()->check(['admin_login', 'admin_appearance'])) {
            return redirect(route('maintenance'), 302);
        }

        $query = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted');

        if (false === auth()->check('admin_login')) {
            $query->whereNotNull('active');
        }

        if (null === $type = $query->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
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

        $listings = \App\Models\Listing::search((new \App\Models\Listing(['type_id' => $type->id])))
            ->where('user_id', auth()->user()->id)
            ->where('type_id', $type->id)
            ->with([
                'user',
                'reviews',
            ])
            ->paginate();

        $table = dataTable($listings)
            ->addColumns([
                'id' => [__('listing.datatable.label.id')],
                'title' => [__('listing.datatable.label.title')],
                'active' => [__('listing.datatable.label.status'), function ($listing) {
                    return view('misc/status', [
                        'type' => 'listing',
                        'status' => $listing->active,
                    ]);
                }],                        
                'status' => [__('listing.datatable.label.order_status'), function ($listing) {
                    return view('misc/status', [
                        'type' => 'order',
                        'status' => $listing->status,
                    ]);
                }],
            ]);

        $table
            ->orderColumns([
                'id',
                'title',
            ])
            ->addActions([
                'preview' => [__('listing.datatable.action.preview'), function ($listing) use ($type) {
                    if (null !== $listing->get('_page')) {
                        return route($type->slug . '/' . $listing->slug);
                    }
                }, true],
                'summary' => [__('listing.datatable.action.summary'), function ($listing) use ($type) {
                    return route('account/manage/' . $type->slug . '/summary/' . $listing->slug);
                }],
                'edit' => [__('listing.datatable.action.edit'), function ($listing) use ($type) {
                    return route('account/manage/' . $type->slug . '/update/' . $listing->slug);
                }],
                'reviews' => [
                    function ($listing) {
                        return __('listing.datatable.action.reviews', ['count' => '<span class="badge badge-pill badge-secondary">' . $listing->reviews->count() . '</span>']);
                    },
                    function ($listing) use ($type) {
                        if (null !== $type->reviewable && null !== $listing->get('_reviews')) {
                            return route('account/manage/' . $type->slug . '/reviews/' . $listing->slug);
                        }
                    }
                ],
            ]);

        $form = form()
            ->setMethod('get')
            ->setTemplate('form/inline')
            ->add('keyword', 'text', [
                'placeholder' => __('listing.searchform.label.keyword'),
                'weight' => 10
            ])
            ->add('submit', 'submit', [
                'label' => __('listing.searchform.label.submit')
            ])
            ->forceRequest();

        $data = collect([
            'page' => $page,
            'html' => view('account/listings/index', [
                'listings' => $table,
                'type' => $type,
                'form' => $form,
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

    public function actionSummary($params)
    {
        if (null === $page = \App\Models\Page::where('slug', 'account/manage/type/summary')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ((null !== config()->general->maintenance || null === $page->active) && false === auth()->check(['admin_login', 'admin_appearance'])) {
            return redirect(route('maintenance'), 302);
        }

        $query = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted');

        if (false === auth()->check('admin_login')) {
            $query->whereNotNull('active');
        }

        if (null === $type = $query->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()
            ->setTitle($page->title)
            ->setMeta('title', $page->meta_title)
            ->setMeta('keywords', $page->meta_keywords)
            ->setMeta('description', $page->meta_description);

        if (null === $listing = auth()->user()->listings()->where('type_id', $type->id)->where('slug', $params['slug'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null !== $listing->get('_backlink') && null !== $listing->get('backlinkchecker_retry')) {
            $info = view('misc/backlink', [
                'url' => ('' != config()->other->get('backlinkchecker_url', '') ? config()->other->get('backlinkchecker_url') : config()->app->url),
                'template' => config()->other->get('backlinkchecker_url_template', ''),
            ]);
        }

        $upgrades = (new \App\Models\Product())->getTreeWithPricing($type->id, $listing->category_id, ('cancelled' == $listing->order->status ? null : $listing->order->pricing_id));

        $form = form();

        if (count($upgrades) > 0) {
            $form
                ->add('pricing_id', 'tree', [
                    'label' => __('listing.form.label.pricing'), 
                    'value' => $listing->pricing_id,
                    'tree_source' => $upgrades,
                    'constraints' => 'maxlength:1'
                ])
                ->add('submit_change', 'submit', ['label' => __('listing.form.label.change_pricing')]);
        }

        if ('cancelled' != $listing->status && null !== $listing->order->cancellable && null === $listing->order->get('subscription_id')) {
            $form->add('submit_cancel', 'submit', ['label' => __('listing.form.label.cancel_pricing')]);
        }

        $form->handleRequest(['submit_change', 'submit_cancel']);

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                if (null !== request()->post->get('submit_change')) {
                    if (count($upgrades) < 1 || null === $listing->order->pricing) {
                        $form->setValidationError('pricing_id', __('pricing.alert.upgrade_not_allowed'));
                    }

                    if (false === isset($input['pricing_id'][0])) {
                        $form->setValidationError('pricing_id', __('form.validation.required'));
                    }

                    if (isset($input['pricing_id'][0])) {
                        $to = \App\Models\Pricing::find($input['pricing_id'][0]);

                        if (null === $to) {
                            $form->setValidationError('pricing_id', __('pricing.alert.not_found'));
                        } else {
                            if (false === $listing->order->pricing->upgrades->contains('id', $to->id) && 'cancelled' != $listing->order->status) {
                                $form->setValidationError('pricing_id', __('pricing.alert.upgrade_not_allowed'));
                            }
                        }
                    }
                } elseif (null !== request()->post->get('submit_cancel')) {
                    if ('cancelled' == $listing->status || null === $listing->order->cancellable || null !== $listing->order->get('subscription_id')) {
                        throw new \App\Src\Http\NotFoundHttpException();
                    }
                }
            }

            if ($form->isValid()) {
                if (null !== request()->post->get('submit_change')) {
                    if ($input['pricing_id'][0] != $listing->pricing_id || 'cancelled' == $listing->status) {
                        $old = \App\Models\Pricing::find($listing->order->pricing_id);
                        $new = \App\Models\Pricing::find($input['pricing_id'][0]);
                        
                        $listing->order->activate($input['pricing_id'][0]);

                        (new \App\Repositories\EmailQueue())->push(
                            'admin_order_changed',
                            null,
                            [
                                'id' => $listing->user->id,
                                'first_name' => $listing->user->first_name,
                                'last_name' => $listing->user->last_name,
                                'email' => $listing->user->email,

                                'listing_id' => $listing->id,
                                'listing_title' => $listing->title,
                                'listing_product' => $old->getNameWithProduct(),
                                'listing_new_product' => $new->getNameWithProduct(),
                                'listing_type_singular' => $type->name_singular,
                                'listing_type_plural' => $type->name_plural,

                                'link' => adminRoute('manage/' . $listing->type->slug . '/summary/' . $listing->id),
                            ],
                            [config()->email->from_email => config()->email->from_name],
                            [config()->email->from_email => config()->email->from_name]
                        );
                    }

                    return redirect(route('account/manage/' . $type->slug, session()->get('account/manage/' . $type->slug)))
                        ->with('success', view('flash/success', ['message' => __('listing.form.alert.change_pricing.success')]));
                } elseif (null !== request()->post->get('submit_cancel')) {
                    $listing->order->deactivate();

                    (new \App\Repositories\EmailQueue())->push(
                        'admin_order_cancelled',
                        null,
                        [
                            'id' => $listing->user->id,
                            'first_name' => $listing->user->first_name,
                            'last_name' => $listing->user->last_name,
                            'email' => $listing->user->email,

                            'listing_id' => $listing->id,
                            'listing_title' => $listing->title,
                            'listing_product' => $listing->order->pricing->getNameWithProduct(),
                            'listing_type_singular' => $type->name_singular,
                            'listing_type_plural' => $type->name_plural,

                            'link' => adminRoute('manage/' . $listing->type->slug . '/summary/' . $listing->id),
                        ],
                        [config()->email->from_email => config()->email->from_name],
                        [config()->email->from_email => config()->email->from_name]
                    );

                    (new \App\Repositories\EmailQueue())->push(
                        'user_order_cancelled',
                        $listing->user->id,
                        [
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
                        ],
                        [$listing->user->email => $listing->user->getName()],
                        [config()->email->from_email => config()->email->from_name]
                    );

                    return redirect(route('account/manage/' . $type->slug, session()->get('account/manage/' . $type->slug)))
                        ->with('success', view('flash/success', ['message' => __('listing.form.alert.cancel_pricing.success')]));
                }
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        $data = collect([
            'page' => $page,
            'html' => view('account/listings/summary', [
                'listing' => $listing,
                'type' => $type,
                'form' => $form,
                'alert' => $alert ?? null,
                'info' => $info ?? null,
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

    public function actionCreate($params)
    {
        if (null === $page = \App\Models\Page::where('slug', 'account/manage/type/create')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ((null !== config()->general->maintenance || null === $page->active) && false === auth()->check(['admin_login', 'admin_appearance'])) {
            return redirect(route('maintenance'), 302);
        }

        $query = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted');

        if (false === auth()->check('admin_login')) {
            $query->whereNotNull('active');
        }

        if (null === $type = $query->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ($type->peruser_limit > 0 && $type->peruser_limit <= auth()->user()->listings()->where('type_id', $type->id)->count()) {
            return redirect(route('account/manage/' . $type->slug))
                ->with('error', view('flash/error', ['message' => __('type.alert.peruser_limit_reached')]));
        }

        layout()
            ->setTitle($page->title)
            ->setMeta('title', $page->meta_title)
            ->setMeta('keywords', $page->meta_keywords)
            ->setMeta('description', $page->meta_description);

        if (null === request()->get->get('pricing_id')) {
            $form = form()
                ->add('pricing_id', 'tree', [
                    'label' => __('listing.form.label.pricing'), 
                    'tree_source' => (new \App\Models\Product())->getTreeWithPricing($type->id),
                    'constraints' => 'required|maxlength:1'
                ])
                ->add('submit_product', 'submit', ['label' => __('listing.form.label.submit_product')])
                ->add('compare', 'button', ['label' => __('listing.form.label.compare'), 'attributes' => ['href' => route($type->slug . '/pricing'), 'target' => '_blank']])
                ->handleRequest('submit_product');

            if ($form->isSubmitted()) {
                $input = $form->getValues();

                if ($form->isValid()) {
                    return redirect(route('account/manage/' . $type->slug . '/create', ['pricing_id' => $input['pricing_id'][0]]));
                } else {
                    $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
                }
            }
        } else if (null === request()->get->get('category_id')) {
            $form = form()
                ->add('category_id', 'tree', [
                    'label' => __('listing.form.label.category'), 
                    'tree_source' => (new \App\Models\Category())->getTree($type->id, request()->get->get('pricing_id')),
                    'constraints' => 'required|maxlength:1'
                ])
                ->add('submit_category', 'submit', ['label' => __('listing.form.label.submit_category')])
                ->handleRequest('submit_category');

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $input = $form->getValues();

                    return redirect(route('account/manage/' . $type->slug . '/create', ['pricing_id' => request()->get->pricing_id, 'category_id' => $input['category_id'][0]]));
                } else {
                    $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
                }
            }
        } else {
            if (null === $pricing = \App\Models\Pricing::where('id', request()->get->get('pricing_id'))->whereNull('hidden')->first()) {
                return redirect(route('account/manage/' . $type->slug . '/create'))
                    ->with('error', view('flash/error', ['message' => __('pricing.alert.not_found')]));
            }

            if ($pricing->user_limit > 0 && $pricing->user_limit <= \App\Models\User::where('id', '!=', auth()->user()->id)->whereHas('orders', function ($query) use ($pricing) { $query->where('pricing_id', $pricing->id); })->count()) {
                return redirect(route('account/manage/' . $type->slug . '/create'))
                    ->with('error', view('flash/error', ['message' => __('pricing.alert.user_limit_reached')]));
            }

            if ($pricing->peruser_limit > 0 && $pricing->peruser_limit <= auth()->user()->orders()->where('pricing_id', $pricing->id)->count()) {
                return redirect(route('account/manage/' . $type->slug . '/create'))
                    ->with('error', view('flash/error', ['message' => __('pricing.alert.peruser_limit_reached')]));
            }

            if (null !== $pricing->product->get('_backlink')) {
                $info = view('misc/backlink', [
                    'url' => ('' != config()->other->get('backlinkchecker_url', '') ? config()->other->get('backlinkchecker_url') : config()->app->url),
                    'template' => config()->other->get('backlinkchecker_url_template', ''),
                ]);
            }

            $required = $pricing->required()->with('product')->get();

            if ($required->count() > 0) {
                if (auth()->user()->orders()->whereIn('pricing_id', $required->pluck('id')->all())->where('status', 'active')->count() < 1) {
                    return redirect(route('account/manage/' . $type->slug . '/create'))
                        ->with('error', view('flash/error', ['message' => __('pricing.alert.required', ['pricings' => $required->pluck(function ($pricing) {
                            return $pricing->getNameWithProductAndType();
                        })->implode('<br>')])]));
                }
            }

            if (null === $category = $pricing->product->categories()->where((new \App\Models\Category)->getPrefixedTable() . '.id', request()->get->get('category_id'))->first()) {
                return redirect(route('account/manage/' . $type->slug . '/create', ['pricing_id' => $pricing->id]))
                    ->with('error', view('flash/error', ['message' => __('category.alert.not_found')]));
            }
            
            $listing = new \App\Models\Listing();
            $listing->user_id = auth()->user()->id;
            $listing->type_id = $type->id;
            $listing->category_id = $category->id;
            $listing->rating = 0;
            $listing->review_count = 0;

            $order = new \App\Models\Order();
            $order->user_id = auth()->user()->id;
            $order->pricing_id = $pricing->id;

            $form = form();

            $form->add('category', 'ro', [
                    'label' => __('listing.form.label.category'),
                    'value' => $listing->getOutputableValue('_category'),
                ]);

            if ($pricing->product->_extra_categories > 0) {
                $form
                    ->add('categories', 'tree', [
                        'label' => __('listing.form.label.extra_categories'), 
                        'tree_source' => (new \App\Models\Category())->getTree($type->id, $pricing->id, true, $category->id),
                        'constraints' => 'maxlength:' . $pricing->product->_extra_categories
                    ]);
            }

            $query = $type->parents()->whereNull('deleted');
        
            if (false === auth()->check('admin_login')) {
                $query->whereNotNull('active');
            }

            $parents = $query->get();

            if ($parents->count() > 0) {
                foreach ($parents as $parent) {
                    $form
                        ->add('parent_' . $parent->id, 'listing', [
                            'label' => __('listing.form.label.parent', ['singular' => $parent->name_singular, 'plural' => $parent->name_plural]),
                            'type' => $parent->id,
                            'constraints' => 'listing:' . $parent->id,
                        ]);
/*
                        ->add('parent_' . $parent->id, 'tree', [
                            'label' => __('listing.form.label.parent', ['singular' => $parent->name_singular, 'plural' => $parent->name_plural]),
                            'tree_source' => (new \App\Models\Listing())->getTree($parent->id, auth()->user()->id),
                        ]);
*/
                }
            }

            $form->bindModel($listing, 'submit', ['pricing_id' => $pricing->id])
                ->add('submit', 'submit', ['label' => __('listing.form.label.submit')]);

            $form->get('title')->addConstraint('maxlength:' . $pricing->product->_title_size);

            if (null !== $pricing->product->get('_backlink')) {
                $form->get('website')
                    ->addConstraint('required')
                    ->addConstraint('backlink');
            }

            if ($pricing->product->get('_short_description_size') < 1) {
                $form->remove('short_description');
            } else {
                $form->get('short_description')->addConstraint('maxlength:' . $pricing->product->_short_description_size);
            }

            $form->get('description')->addConstraint('htmlmaxtags:a,' . $pricing->product->_description_links_limit);

            if ($pricing->product->get('_description_size') < 1) {
                $form->remove('description');
            } else {
                $form->get('description')->addConstraint('htmlmaxlength:' . $pricing->product->_description_size);
            }

            $form->handleRequest();

            if ($form->isSubmitted()) {
                $input = $form->getValues();

                if ($form->isValid()) {
                    if ('Event' == $type->type) {
                        $dates = $listing->getEventDates();

                        if (count($dates) == 0) {
                            $form->setValidationError('event_start_datetime', __('listing.alert.event_no_dates'));
                        }

                        if (strtotime($input->get('event_start_datetime')) > strtotime($input->get('event_end_datetime'))) {
                            $form->setValidationError('event_end_datetime', __('listing.alert.event_start_after_end'));
                        }

                        if (count($dates) > $pricing->product->_event_dates) {
                            $form->setValidationError('event_end_datetime', __('listing.form.alert.dates_limit_reached', ['limit' => $pricing->product->_event_dates]));
                        }

                        if ('custom' == $input->get('event_frequency') && '' == $input->get('event_dates', '')) {
                            $form->setValidationError('event_dates', __('form.validation.required'));
                        }
                    }

                    if ('Offer' == $type->type) {
                        if (strtotime($input->get('offer_start_datetime')) > strtotime($input->get('offer_end_datetime'))) {
                            $form->setValidationError('offer_end_datetime', __('listing.alert.offer_start_after_end'));
                        }

                        if ('percentage' == $input->get('offer_discount_type') && ($input->get('offer_discount') < 0 || $input->get('offer_discount') > 100)) {
                            $form->setValidationError('offer_discount', __('listing.alert.offer_invalid_percentage'));
                        }
                    }

                    if (\App\Models\Listing::where('slug', $input->get('slug'))->where('type_id', $type->id)->count() > 0) {
                        $form->setValidationError('slug', __('form.validation.unique'));
                    }
                }

                if ($form->isValid()) {
                    if (null === $type->approvable || null !== $pricing->autoapprovable) {
                        $listing->active = 1;
                    }

                    $listing->claimed = 1;                    
                    $listing->added_datetime = date("Y-m-d H:i:s");
                    
                    $listing->saveWithData($input);

                    $listing->order()->save($order);

                    $order->activate($pricing->id);

                    if ('Event' == $type->type) {
                        $listing->saveEventDates($dates);
                    }

                    $listing->categories()->sync($input->get('categories') ?? []);

                    $parentListings = [];

                    foreach ($parents as $parent) {
//                        $parents = array_merge($parents, $input->get('parent_' . $parent->id, []));

                        if (null !== $input->get('parent_' . $parent->id)) {
                            $parentListings[] = $input->get('parent_' . $parent->id);
                        }
                    }

                    $listing->parents()->attach($parentListings);

                    (new \App\Repositories\EmailQueue())->push(
                        (null === $listing->active ? 'admin_listing_created_approve' : 'admin_listing_created'),
                        null,
                        [
                            'id' => auth()->user()->id,
                            'first_name' => auth()->user()->first_name,
                            'last_name' => auth()->user()->last_name,
                            'email' => auth()->user()->email,

                            'listing_id' => $listing->id,
                            'listing_title' => $listing->title,
                            'listing_type_singular' => $type->name_singular,
                            'listing_type_plural' => $type->name_plural,

                            'link' => adminRoute('manage/' . $type->slug . '/approve'),
                        ],
                        [config()->email->from_email => config()->email->from_name],
                        [config()->email->from_email => config()->email->from_name]
                    );

                    if ($order->invoice->status != 'paid') {
                        return redirect(route('account/checkout/' . $order->invoice->id));
                    }

                    return redirect(route('account/manage/' . $type->slug, session()->get('account/manage/' . $type->slug)))
                        ->with('success', view('flash/success', [
                            'message' => (null === $type->approvable || null !== $listing->order->pricing->autoapprovable ? __('listing.form.alert.create.success') : __('listing.form.alert.create_with_moderation.success'))
                        ]));
                } else {
                    $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
                }
            }
        }

        $data = collect([
            'page' => $page,
            'html' => view('account/listings/create', [
                'alert' => $alert ?? null,
                'info' => $info ?? null,
                'form' => $form,
                'type' => $type,
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

    public function actionUpdate($params)
    {
        if (null === $page = \App\Models\Page::where('slug', 'account/manage/type/update')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ((null !== config()->general->maintenance || null === $page->active) && false === auth()->check(['admin_login', 'admin_appearance'])) {
            return redirect(route('maintenance'), 302);
        }

        $query = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted');

        if (false === auth()->check('admin_login')) {
            $query->whereNotNull('active');
        }

        if (null === $type = $query->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()
            ->setTitle($page->title)
            ->setMeta('title', $page->meta_title)
            ->setMeta('keywords', $page->meta_keywords)
            ->setMeta('description', $page->meta_description);

        if (null === $listing = auth()->user()->listings()->where('type_id', $type->id)->where('slug', $params['slug'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ($listing->update()->count() > 0) {
            $alert = view('flash/primary', ['message' => __('listing.alert.update_pending')]);
        }

        $form = form()
            ->add('category', 'ro', [
                'label' => __('listing.form.label.category'),
                'value' => $listing->getOutputableValue('_category'),
            ]);

        if ($listing->order->pricing->product->_extra_categories > 0) {
            $form
                ->add('categories', 'tree', [
                    'label' => __('listing.form.label.extra_categories'), 
                    'tree_source' => (new \App\Models\Category())->getTree($type->id, $listing->order->pricing_id, true, $listing->category_id),
                    'constraints' => 'maxlength:' . $listing->order->pricing->product->_extra_categories,
                ]);
        }

        $query = $type->parents()->whereNull('deleted');
    
        if (false === auth()->check('admin_login')) {
            $query->whereNotNull('active');
        }

        $parents = $query->get();

        if ($parents->count() > 0) {
            $parentListings = $listing->parents()->get();
            
            foreach ($parents as $parent) {
                $form
                    ->add('parent_' . $parent->id, 'listing', [
                        'label' => __('listing.form.label.parent', ['singular' => $parent->name_singular, 'plural' => $parent->name_plural]),
                        'type' => $parent->id,
                        'constraints' => 'listing:' . $parent->id,
                    ])
/*
                    ->add('parent_' . $parent->id, 'tree', [
                        'label' => __('listing.form.label.parent', ['type' => $parent->name_singular]),
                        'tree_source' => (new \App\Models\Listing())->getTree($parent->id),
                    ])
*/
                    ->setValue('parent_' . $parent->id, (null !== $parentListings->where('type_id', $parent->id)->first() ? $parentListings->where('type_id', $parent->id)->first()->id : null));
            }
        }

        $form
            ->bindModel($listing, 'update', ['pricing_id' => $listing->order->pricing_id])
            ->add('submit', 'submit', ['label' => __('listing.form.label.update')])
            ->setValues($listing->data->pluck('value', 'field_name')->all())
            ->setValue('categories', $listing->categories->pluck('id')->all());

        $form->get('title')->addConstraint('maxlength:' . $listing->_title_size);

        if (null !== $listing->get('_backlink')) {
            if (null != $form->get('website')) {
                $form->get('website')
                    ->addConstraint('required')
                    ->addConstraint('backlink');
            }
        }

        if ($listing->get('_short_description_size') < 1) {
            $form->remove('short_description');
        } else {
            $form->get('short_description')->addConstraint('maxlength:' . $listing->get('_short_description_size'));
        }

        $form->get('description')->addConstraint('htmlmaxtags:a,' . $listing->get('_description_links_limit'));

        if ($listing->get('_description_size') < 1) {
            $form->remove('description');
        } else {
            $form->get('description')->addConstraint('htmlmaxlength:' . $listing->get('_description_size'));
        }

        $form->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();
            
            if ($form->isValid()) {
                if ('Event' == $type->type) {
                    $dates = $listing->getEventDates();

                    if (count($dates) == 0) {
                        $form->setValidationError('event_start_datetime', __('listing.alert.event_no_dates'));
                    }

                    if (strtotime($input->get('event_start_datetime')) > strtotime($input->get('event_end_datetime'))) {
                        $form->setValidationError('event_end_datetime', __('listing.alert.event_start_after_end'));
                    }

                    if (count($dates) > $listing->get('_event_dates')) {
                        $form->setValidationError('event_end_datetime', __('listing.form.alert.dates_limit_reached', ['limit' => $listing->get('_event_dates')]));
                    }

                    if ('custom' == $input->get('event_frequency') && '' == $input->get('event_dates', '')) {
                        $form->setValidationError('event_dates', __('form.validation.required'));
                    }
                }

                if ('Offer' == $type->type) {
                    if (strtotime($input->get('offer_start_datetime')) > strtotime($input->get('offer_end_datetime'))) {
                        $form->setValidationError('offer_end_datetime', __('listing.alert.offer_start_after_end'));
                    }

                    if ('percentage' == $input->get('offer_discount_type') && ($input->get('offer_discount') < 0 || $input->get('offer_discount') > 100)) {
                        $form->setValidationError('offer_discount', __('listing.alert.offer_invalid_percentage'));
                    }
                }

                if (\App\Models\Listing::where('id', '!=', $listing->id)->where('slug', $input->get('slug'))->where('type_id', $type->id)->count() > 0) {
                    $form->setValidationError('slug', __('form.validation.unique'));
                }
            }

            if ($form->isValid()) {
                $parentListings = [];

                foreach ($parents as $parent) {
//                    $parents = array_merge($parents, $input->get('parent_' . $parent->id, []));

                    if (null !== $input->get('parent_' . $parent->id)) {
                        $parentListings[] = $input->get('parent_' . $parent->id);
                    }
                }

                (new \App\Repositories\EmailQueue())->push(
                    (null !== $type->approvable_updates ? 'admin_listing_updated_approve' : 'admin_listing_updated'),
                    null,
                    [
                        'id' => auth()->user()->id,
                        'first_name' => auth()->user()->first_name,
                        'last_name' => auth()->user()->last_name,
                        'email' => auth()->user()->email,

                        'listing_id' => $listing->id,
                        'listing_title' => $listing->title,
                        'listing_type_singular' => $type->name_singular,
                        'listing_type_plural' => $type->name_plural,

                        'link' => adminRoute('manage/' . $type->slug . '/approve-updates'),
                    ],
                    [config()->email->from_email => config()->email->from_name],
                    [config()->email->from_email => config()->email->from_name]
                );

                if (null !== $type->approvable_updates) {
                    $update = (new \App\Models\Update())
                        ->import($listing)
                        ->fill($input);
                    
                    $update->added_datetime = date('Y-m-d H:i:s');
                    
                    $update->saveWithData($input);

                    $update->categories()->sync($input->categories ?? []);

                    $update->parents()->sync($parentListings);

                    return redirect(route('account/manage/' . $type->slug, session()->get('account/manage/' . $type->slug)))
                        ->with('success', view('flash/success', ['message' => __('listing.form.alert.update_with_moderation.success')]));
                } else {
                    $listing->updated_datetime = date("Y-m-d H:i:s");

                    $listing->deadlinkchecker_datetime = null;
                    $listing->deadlinkchecker_retry = null;

                    $listing->backlinkchecker_datetime = null;
                    $listing->backlinkchecker_retry = null;

                    $listing->saveWithData($input);

                    $listing->categories()->sync($input->categories ?? []);

                    if ('Event' == $type->type) {
                        $listing->saveEventDates($dates);
                    }

                    $listing->parents()->sync($parentListings);

                    return redirect(route('account/manage/' . $type->slug, session()->get('account/manage/' . $type->slug)))
                        ->with('success', view('flash/success', ['message' => __('listing.form.alert.update.success')]));
                }
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        $data = collect([
            'page' => $page,
            'html' => view('account/listings/update', [
                'listing' => $listing,
                'type' => $type,
                'form' => $form,
                'alert' => $alert ?? null,
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

    public function actionReviews($params)
    {
        if (null === $page = \App\Models\Page::where('slug', 'account/manage/type/reviews')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ((null !== config()->general->maintenance || null === $page->active) && false === auth()->check(['admin_login', 'admin_appearance'])) {
            return redirect(route('maintenance'), 302);
        }

        $query = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted');

        if (false === auth()->check('admin_login')) {
            $query->whereNotNull('active');
        }

        if (null === $type = $query->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $listing = auth()->user()->listings()->where('type_id', $type->id)->where('slug', $params['slug'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
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

        $reviews = \App\Models\Review::search()
            ->where('listing_id', $listing->id)
            ->paginate();

        $table = dataTable($reviews)
            ->addColumns([
                'status' => [__('review.datatable.label.status'), function ($review) {
                    return view('misc/status', [
                        'type' => 'review',
                        'status' => $review->active,
                    ]);
                }],
                'title' => [__('review.datatable.label.title')],
                'rating' => [__('review.datatable.label.rating'), function ($review) {
                    return '<p class="m-0 mb-3 text-warning text-nowrap display-11">' . $review->getOutputableValue('_rating') . '</p>';
                }],
                'added_datetime' => [__('review.datatable.label.added_datetime'), function ($review) {
                    return locale()->formatDatetimeDiff($review->added_datetime);
                }],
            ])
            ->orderColumns([
                'title',
                'added_datetime',
            ])
            ->addActions([
                'view' => [__('review.datatable.action.view'), function ($review) {
                    return route('account/reviews/' . $review->id);
                }],
            ]);

        $data = collect([
            'page' => $page,
            'html' => view('account/listings/reviews', [
                'type' => $type,
                'reviews' => $table,
                'listing' => $listing,
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
