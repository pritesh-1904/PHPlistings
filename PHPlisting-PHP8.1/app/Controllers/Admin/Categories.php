<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Categories
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check(['admin_content', 'admin_categories'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ('' == request()->get->get('parent_id', '')) {
            request()->get->parent_id = (new \App\Models\Category)->getRoot($type->id)->id;
        }

        if ('' != request()->get->get('category_id', '')) {
            request()->get->parent_id = '';
        }

        layout()->setTitle(__('admin.categories.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        if (null === request()->get->get('sort')) {
            request()->get->sort = '_left';
            request()->get->sort_direction = 'asc';
        }

        $categories = \App\Models\Category::search(
                (new \App\Models\Category())
                    ->setSortable('_left', '_left', null)
                    ->setSortable('id', 'id', null)
                    ->setSortable('slug', 'slug', null)
                    ->setSortable('impressions', 'impressions', null)
                    ->setSortable('counter', 'counter', null),
                [],
                'admin/categories/' . $type->slug
            )
            ->where('type_id', $type->id)
            ->paginate();

        $table = dataTable($categories)
            ->addColumns([
                'id' => [__('admin.categories.datatable.label.id')],
                'name' => [__('admin.categories.datatable.label.name')],
                'slug' => [__('admin.categories.datatable.label.slug')],
                'impressions' => [__('admin.categories.datatable.label.impressions'), function ($category) {
                    return $category->get('impressions', 0);
                }],
                'counter' => [__('admin.categories.datatable.label.counter'), function ($category) {
                    return $category->get('counter', 0);
                }],
                'active' => [__('admin.categories.datatable.label.published'), function ($category) {
                    return view('misc/ajax-switch', [
                        'table' => 'categories',
                        'column' => 'active',
                        'id' => $category->id,
                        'value' => $category->active
                    ]);
                }],
                'featured' => [__('admin.categories.datatable.label.featured'), function ($category) {
                    return view('misc/ajax-switch', [
                        'table' => 'categories',
                        'column' => 'featured',
                        'id' => $category->id,
                        'value' => $category->featured
                    ]);
                }],

            ])
            ->orderColumns([
                'id',
                'slug',
                'impressions',
                'counter',
            ])
            ->addActions([
                'edit' => [__('admin.categories.datatable.action.edit'), function ($category) use ($type) {
                    return adminRoute('categories/' . $type->slug . '/update/' . $category->id);
                }],
                'children' => [__('admin.categories.datatable.action.children'), function ($category) use ($type) {
                    if (false === $category->isLeaf()) {
                        return adminRoute('categories/' . $type->slug, ['parent_id' => $category->id]);
                    }
                }],
                'delete' => [__('admin.categories.datatable.action.delete'), function ($category) use ($type) {
                    if (false !== $category->isLeaf()) {
                        return adminRoute('categories/' . $type->slug . '/delete/' . $category->id);
                    }
                }],
            ]);

        $form = form()
            ->setMethod('get')
            ->setTemplate('form/inline')
            ->add('category_id', 'category', [
                'type_id' => $type->id,
                'placeholder' => __('admin.categories.searchform.label.category'),
                'weight' => 10,
            ])
            ->add('submit', 'submit', [
                'label' => __('admin.categories.searchform.label.submit')
            ])
            ->forceRequest();

        return response(layout()->content(
            view('admin/categories/index', [
                'type' => $type,
                'parent' => \App\Models\Category::find(request()->get->parent_id),
                'categories' => $table,
                'form' => $form,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check(['admin_content', 'admin_categories'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.categories.title.create', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $category = new \App\Models\Category();

        $form = $this->getForm($category, $type)
            ->add('submit', 'submit', ['label' => __('admin.categories.form.label.submit')])
            ->handleRequest();
        
        if ($form->isSubmitted()) {           
            $input = $form->getValues();

            if (\App\Models\Category::where('slug', $input->get('slug'))->where('type_id', $type->id)->count() > 0) {
                $form->setValidationError('slug', __('form.validation.unique'));
            }

            if ($input->placement != 'root' && !isset($input->category[0])) {
                $form->setValidationError('category', __('form.validation.required'));
            }

            if ($form->isValid()) {
                $category->type_id = $type->id;

                switch($input->placement) {
                    case 'root':
                        $category->appendTo(\App\Models\Category::find((new \App\Models\Category)->getRoot($type->id)->id));
                        break;
                    case 'append':
                        $category->appendTo(\App\Models\Category::find($input->category[0]));
                        break;
                    case 'before':
                        $category->insertBefore(\App\Models\Category::find($input->category[0]));
                        break;
                    case 'after':
                        $category->insertAfter(\App\Models\Category::find($input->category[0]));
                        break;
                }

                $category->save();

                $category->fields()->attach($input->fields);
                $category->products()->attach($input->products);

                return redirect(adminRoute('categories/' . $type->slug, session()->get('admin/categories/' . $type->slug)))
                    ->with('success', view('flash/success', ['message' => __('admin.categories.alert.create.success', ['id' => $category->id, 'name' => $category->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/categories/create', [
                'type' => $type,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (!auth()->check(['admin_content', 'admin_categories'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $category = \App\Models\Category::where('id', $params['id'])->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.categories.title.update', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $form = $this->getForm($category, $type)
            ->add('submit', 'submit', ['label' => __('admin.categories.form.label.update')])
            ->setValue('fields', $category->fields->pluck('id')->all())
            ->setValue('products', $category->products->pluck('id')->all())
            ->removeConstraints('placement')
            ->handleRequest();

        if ($form->isSubmitted()) {           
            $input = $form->getValues();

            if (\App\Models\Category::where('id', '!=', $category->id)->where('slug', $input->get('slug'))->where('type_id', $type->id)->count() > 0) {
                $form->setValidationError('slug', __('form.validation.unique'));
            }

            if ($input->placement !== null && $input->placement != 'root' && !isset($input->category[0])) {
                $form->setValidationError('category', __('form.validation.required'));
            }

            if ($form->isValid()) {
                switch($input->placement) {
                    case 'root':
                        $category->appendTo(\App\Models\Category::find((new \App\Models\Category)->getRoot($type->id)->id));
                        break;
                    case 'append':
                        $category->appendTo(\App\Models\Category::find($input->category[0]));
                        break;
                    case 'before':
                        $category->insertBefore(\App\Models\Category::find($input->category[0]));
                        break;
                    case 'after':
                        $category->insertAfter(\App\Models\Category::find($input->category[0]));
                        break;
                }

                $category->save();

                $category->fields()->sync($input->fields);
                $category->products()->sync($input->products);

                return redirect(adminRoute('categories/' . $type->slug, session()->get('admin/categories/' . $type->slug)))
                    ->with('success', view('flash/success', ['message' => __('admin.categories.alert.update.success', ['id' => $category->id, 'name' => $category->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/categories/update', [
                'type' => $type,
                'form' => $form,
                'alert' => $alert ?? null,
                'category' => $category,
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (!auth()->check(['admin_content', 'admin_categories'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $category = \App\Models\Category::where('id', $params['id'])->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (false === $category->isRoot() && false !== $category->isLeaf()) {
            if ($category->listings()->count() > 0) {
                return redirect(adminRoute('categories/' . $type->slug, session()->get('admin/categories/' . $type->slug)))
                    ->with('error', view('flash/error', ['message' => __('admin.categories.alert.remove.failed', ['id' => $category->id, 'name' => $category->name])]));
            }

            $category->delete();
        }

        return redirect(adminRoute('categories/' . $type->slug, session()->get('admin/categories/' . $type->slug)))
            ->with('success', view('flash/success', ['message' => __('admin.categories.alert.remove.success', ['id' => $category->id, 'name' => $category->name])]));
    }

    public function actionCreateMultiple($params)
    {
        if (!auth()->check(['admin_content', 'admin_categories'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.categories.title.create_multiple', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $form = form()
            ->add('active', 'toggle', ['label' => __('admin.categories.form.label.published'), 'value' => 1])
            ->add('language_id', 'select', [
                'label' => __('admin.categories.form.label.language'),
                'options' => \App\Models\Language::whereNotNull('active')->get()->pluck('name', 'id')->all(),
                'constraints' => 'required',
            ])
            ->add('featured', 'toggle', ['label' => __('admin.categories.form.label.featured')])
            ->add('csv', 'textarea', ['label' => __('admin.categories.form.label.csv'), 'placeholder' => 'e.g.:
Business;Consulting
Business;Services
Education;Colleges & Universities
Education;Educational Services
Entertainment', 'constraints' => 'required'])
            ->add('icon', 'icon', ['label' => __('admin.categories.form.label.icon'), 'value' => 'far fa-circle', 'constraints' => 'required'])
            ->add('marker_color', 'color', ['label' => __('admin.categories.form.label.marker_color'), 'value' => '#FF0000', 'constraints' => 'required'])
            ->add('icon_color', 'color', ['label' => __('admin.categories.form.label.icon_color'), 'value' => '#FFFFFF', 'constraints' => 'required'])
            ->add('fields', 'tree', [
                'label' => __('admin.categories.form.label.fields'), 
                'tree_source' => (new \App\Models\ListingField())->getTree($type->id, 1),
            ])
            ->add('products', 'tree', [
                'label' => __('admin.categories.form.label.products'),
                'tree_source' => (new \App\Models\Product())->getTree($type->id),
            ])
            ->add('submit', 'submit', ['label' => __('admin.categories.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {           
            $input = $form->getValues();

            $language = \App\Models\Language::find($input->get('language_id'));

            if ($form->isValid()) {
                $strings = explode('<br />', \nl2br($input->csv));
                
                foreach ($strings as $string) {
                    $root = (new \App\Models\Category)->getRoot($type->id);

                    $current = $root;                    

                    if ('' == trim($string)) {
                        continue;
                    }

                    $categories = explode(';', d($string));

                    foreach ($categories as $category) {
                        $category = trim(e($category));
                        
                        if ('' == $category) {
                            continue;
                        }

                        $temp = \App\Models\Category::where('type_id', $type->id)
                            ->where('_parent_id', $current->id)
                            ->where('name', 'like', '%"' . $language->locale . '":"' . $category . '"%')
                            ->first();

                        if (null === $temp) {
                            $temp = new \App\Models\Category();
                            $temp->type_id = $type->id;
                            $temp->appendTo($current);
                            $temp->fill([
                                'active' => $input->active,
                                'featured' => $input->featured,
                                'slug' => slugify(d($category)),
                                'icon' => $input->icon,
                                'marker_color' => $input->marker_color,
                                'icon_color' => $input->icon_color,
                                'logo_id' => bin2hex(random_bytes(16)),
                                'header_id' => bin2hex(random_bytes(16)),
                            ]);

                            $temp->setTranslation('name', $category, config()->app->locale_fallback);

                            if (config()->app->locale_fallback != $language->locale) {
                                $temp->setTranslation('name', $category, $language->locale);
                            }
                            
                            $temp->save();

                            $temp->fields()->attach($input->fields);
                            $temp->products()->attach($input->products);
                        }

                        $current = $temp;
                    }
                }
                
                return redirect(adminRoute('categories/' . $type->slug))
                    ->with('success', view('flash/success', ['message' => __('admin.categories.alert.create_multiple.success')]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/categories/create-multiple', [
                'type' => $type,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    private function getForm($model, $type)
    {
        return form($model)
            ->add('active', 'toggle', ['label' => __('admin.categories.form.label.published'), 'value' => '1'])
            ->add('featured', 'toggle', ['label' => __('admin.categories.form.label.featured')])
            ->add('name', 'translatable', ['label' => __('admin.categories.form.label.name'), 'constraints' => 'transrequired'])
            ->add('slug', 'text', ['label' => __('admin.categories.form.label.slug'), 'sluggable' => 'name', 'constraints' => 'required|alphanumericdash|maxlength:120'])
            ->add('icon', 'icon', ['label' => __('admin.categories.form.label.icon'), 'value' => 'far fa-circle', 'constraints' => 'required'])
            ->add('marker_color', 'color', ['label' => __('admin.categories.form.label.marker_color'), 'value' => '#FF0000', 'constraints' => 'required'])
            ->add('icon_color', 'color', ['label' => __('admin.categories.form.label.icon_color'), 'value' => '#FFFFFF', 'constraints' => 'required'])
            ->add('short_description', 'translatable', ['label' => __('admin.categories.form.label.summary')])
            ->add('description', 'textarea', ['label' => __('admin.categories.form.label.description')])
            ->add('placement', 'radio', ['label' => __('admin.categories.form.label.placement'), 'options' => ['root' => __('admin.categories.form.label.new_category'), 'append' => __('admin.categories.form.label.subcategory_of'), 'before' => __('admin.categories.form.label.before'), 'after' => __('admin.categories.form.label.after')], 'constraints' => 'required'])
            ->add('category', 'tree', [
                'tree_source' => (new \App\Models\Category())->getTree($type->id),
                'constraints' => 'maxlength:1',
                'tree_leaves' => false,
            ])
            ->add('fields', 'tree', [
                'label' => __('admin.categories.form.label.fields'),
                'tree_source' => (new \App\Models\ListingField())->getTree($type->id),
            ])
            ->add('products', 'tree', [
                'label' => __('admin.categories.form.label.products'),
                'tree_source' => (new \App\Models\Product())->getTreeWithHidden($type->id),
            ])
            ->add('logo_id', 'dropzone', ['label' => __('admin.categories.form.label.logo'), 'upload_id' => '3'])
            ->add('header_id', 'dropzone', ['label' => __('admin.categories.form.label.header'), 'upload_id' => '5'])
            ->add('meta_title', 'translatable', ['label' => __('admin.categories.form.label.meta_title')])
            ->add('meta_keywords', 'translatable', ['label' => __('admin.categories.form.label.meta_keywords')])
            ->add('meta_description', 'translatable', ['label' => __('admin.categories.form.label.meta_description')]);
    }

}
