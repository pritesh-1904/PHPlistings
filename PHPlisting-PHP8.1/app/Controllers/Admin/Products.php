<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Products
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (false === auth()->check(['admin_content', 'admin_products'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.products.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $products = \App\Models\Product::search(null, [], 'admin/products/' . $type->slug)
            ->where('type_id', $type->id)
            ->orderBy('weight')
            ->with('pricings')
            ->paginate();

        $table = dataTable($products)
            ->addColumns([
                'id' => [__('admin.products.datatable.label.id')],
                'name' => [__('admin.products.datatable.label.name')],
                'hidden' => [__('admin.products.datatable.label.hidden'), function ($product) {
                    return view('misc/ajax-switch', [
                        'table' => 'products',
                        'column' => 'hidden',
                        'id' => $product->id,
                        'value' => $product->hidden,
                    ]);
                }],
                'featured' => [__('admin.products.datatable.label.featured'), function ($product) {
                    return view('misc/ajax-switch', [
                        'table' => 'products',
                        'column' => 'featured',
                        'id' => $product->id,
                        'value' => $product->featured,
                    ]);
                }],
            ])
            ->addActions([
                'edit' => [__('admin.products.datatable.action.edit'), function ($product) use ($type) {
                    return adminRoute('products/' . $type->slug . '/update/' . $product->id);
                }],
                'pricings' => [
                    function ($product) {
                        return __('admin.products.datatable.action.pricings', ['count' => '<span class="badge badge-secondary">' . $product->pricings->count() . '</span>']);
                    }, function ($product) use ($type) {
                        return adminRoute('pricings/' . $type->slug, ['product_id' => $product->id]);
                    },
                ],
                'delete' => [__('admin.products.datatable.action.delete'), function ($product) use ($type) {
                    return adminRoute('products/' . $type->slug . '/delete/' . $product->id);
                }],
            ])
            ->setSortable('products');

        return response(layout()->content(
            view('admin/products/index', [
                'type' => $type,
                'products' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (false === auth()->check(['admin_content', 'admin_products'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.products.title.create', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $product = new \App\Models\Product();
        $product->type_id = $type->id;

        $form = $this->getForm($product, $type)
            ->add('submit', 'submit', ['label' => __('admin.products.form.label.submit')])
            ->handleRequest();
        
        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $product->save();

                $product->categories()->attach($form->getValues()->categories);
                $product->fields()->attach($form->getValues()->fields);
                $product->badges()->attach($form->getValues()->badges ?? []);

                return redirect(adminRoute('products/' . $type->slug, session()->get('admin/products/' . $type->slug)))
                    ->with('success', view('flash/success', ['message' => __('admin.products.alert.create.success', ['name' => $product->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/products/create', [
                'type' => $type,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (false === auth()->check(['admin_content', 'admin_products'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $product = \App\Models\Product::where('id', $params['id'])->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.products.title.update', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $form = $this->getForm($product, $type)
            ->add('sync', 'toggle', ['label' => __('admin.products.form.label.sync'), 'value' => 1])
            ->add('submit', 'submit', ['label' => __('admin.products.form.label.update')])
            ->setValues([
                'categories' => $product->categories->pluck('id')->all(),
                'fields' => $product->fields->pluck('id')->all(),
                'badges' => $product->badges->pluck('id')->all()
            ])
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                $product->save();

                $product->categories()->sync($input->categories);
                $product->fields()->sync($input->fields);
                $product->badges()->sync($input->badges ?? []);

                if (null !== $input->get('sync') && $product->pricings->count() > 0) {
                    \App\Models\Listing::whereHas('order', function ($query) use ($product) {
                            $query->whereIn('pricing_id', $product->pricings->pluck('id')->all());
                        })
                        ->where('type_id', $type->id)
                        ->update(['sync_product' => 1]);
                }

                return redirect(adminRoute('products/' . $type->slug, session()->get('admin/products/' . $type->slug)))
                    ->with('success', view('flash/success', ['message' => __('admin.products.alert.update.success', ['name' => $product->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/products/update', [
                'type' => $type,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (false === auth()->check(['admin_content', 'admin_products'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $product = \App\Models\Product::where('id', $params['id'])->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ($product->pricings()->count() > 0) {
            return redirect(adminRoute('products/' . $type->slug, session()->get('admin/products/' . $type->slug)))
                ->with('error', view('flash/error', ['message' => __('admin.products.alert.remove.failed', ['name' => $product->name])]));
        }

        $product->delete();

        return redirect(adminRoute('products/' . $type->slug, session()->get('admin/products/' . $type->slug)))
            ->with('success', view('flash/success', ['message' => __('admin.products.alert.remove.success', ['name' => $product->name])]));
    }

    private function getForm($model, $type)
    {
        $form = form($model)
            ->add('hidden', 'toggle', ['label' => __('admin.products.form.label.hidden')])
            ->add('featured', 'toggle', ['label' => __('admin.products.form.label.featured')])
            ->add('name', 'translatable', ['label' => __('admin.products.form.label.name'), 'constraints' => 'transrequired'])
            ->add('description', 'translatable', ['label' => __('admin.products.form.label.description')])
            ->add('_featured', 'toggle', ['label' => __('admin.products.form.label.featured_listing')])
            ->add('_position', 'number', ['label' => __('admin.products.form.label.position'), 'value' => 0, 'constraints' => 'required|min:0'])
            ->add('_page', 'toggle', ['label' => __('admin.products.form.label.page'), 'value' => 1])
            ->add('_extra_categories', 'number', ['label' => __('admin.products.form.label.extra_categories'), 'value' => 5, 'constraints' => 'required|min:0'])
            ->add('_title_size', 'number', ['label' => __('admin.products.form.label.title_size'), 'value' => 150, 'constraints' => 'required|min:1|max:200'])
            ->add('_short_description_size', 'number', ['label' => __('admin.products.form.label.short_description_size'), 'value' => 150, 'constraints' => 'required|min:0'])
            ->add('_description_size', 'number', ['label' => __('admin.products.form.label.description_size'), 'value' => 2500, 'constraints' => 'required|min:0'])
            ->add('_description_links_limit', 'number', ['label' => __('admin.products.form.label.description_links_limit'), 'value' => 0, 'constraints' => 'required|min:0'])
            ->add('_gallery_size', 'number', ['label' => __('admin.products.form.label.gallery_size'), 'value' => 5, 'constraints' => 'required|min:0'])
            ->add('_address', 'toggle', ['label' => __('admin.products.form.label.address')])
            ->add('_map', 'toggle', ['label' => __('admin.products.form.label.map')])
            ->add('_event_dates', 'number', ['label' => __('admin.products.form.label.event_dates'), 'value' => 5, 'constraints' => 'required|min:1'])
            ->add('_send_message', 'toggle', ['label' => __('admin.products.form.label.send_message')])
            ->add('_reviews', 'toggle', ['label' => __('admin.products.form.label.reviews')])
            ->add('_seo', 'toggle', ['label' => __('admin.products.form.label.seo')])
            ->add('_backlink', 'toggle', ['label' => __('admin.products.form.label.backlink')])
            ->add('_dofollow', 'toggle', ['label' => __('admin.products.form.label.dofollow')])
            ->add('categories', 'tree', [
                'label' => __('admin.products.form.label.categories'),
                'tree_source' => (new \App\Models\Category())->getExpandedTree($type->id),
            ])
            ->add('fields', 'tree', [
                'label' => __('admin.products.form.label.fields'),
                'tree_source' => (new \App\Models\ListingField())->getTree($type->id),
            ]);

        if ($type->badges()->count() > 0) {
            $form
                ->add('badges', 'badges', [
                    'label' => __('admin.products.form.label.badges'),
                    'type_id' => $type->id,
                ]);
        }

        return $form;
    }

}
