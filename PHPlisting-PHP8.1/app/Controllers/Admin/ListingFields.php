<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class ListingFields
    extends \App\Controllers\Admin\BaseController
{

    public $types;

    public function __construct()
    {
        parent::__construct();

        $this->types = [
            'captcha' => __('admin.listingfields.type.captcha'),
            'checkbox' => __('admin.listingfields.type.checkbox'),
            'color' => __('admin.listingfields.type.color'),
            'date' => __('admin.listingfields.type.date'),
            'dates' => __('admin.listingfields.type.dates'),
            'datetime' => __('admin.listingfields.type.datetime'),
            'dropzone' => __('admin.listingfields.type.file'),
            'email' => __('admin.listingfields.type.email'),
            'hidden' => __('admin.listingfields.type.hidden'),
            'hours' => __('admin.listingfields.type.hours'),
            'htmltextarea' => __('admin.listingfields.type.htmltextarea'),
            'keywords' => __('admin.listingfields.type.keywords'),
            'locationmappicker' => __('admin.listingfields.type.locationmappicker'),
            'mselect' => __('admin.listingfields.type.mselect'),
            'number' => __('admin.listingfields.type.number'),
            'password' => __('admin.listingfields.type.password'),
            'phone' => __('admin.listingfields.type.phone'),
            'price' => __('admin.listingfields.type.price'),
            'radio' => __('admin.listingfields.type.radio'),
            'rating' => __('admin.listingfields.type.rating'),
            'ro' => __('admin.listingfields.type.readonly'),
            'select' => __('admin.listingfields.type.select'),
            'separator' => __('admin.listingfields.type.separator'),
            'social' => __('admin.listingfields.type.social'),
            'text' => __('admin.listingfields.type.text'),
            'textarea' => __('admin.listingfields.type.textarea'),
            'time' => __('admin.listingfields.type.time'),
            'timezone' => __('admin.listingfields.type.timezone'),
            'toggle' => __('admin.listingfields.type.toggle'),
            'url' => __('admin.listingfields.type.url'),
            'youtube' => __('admin.listingfields.type.youtube'),
        ];
    }

    public function actionIndex($params)
    {
        if (false === auth()->check(['admin_content', 'admin_fields'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\ListingFieldGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (
            ('listings' == $group->slug && false === auth()->check('admin_listings')) || 
            ('messages' == $group->slug && false === auth()->check('admin_messages')) ||
            ('reviews' == $group->slug && false === auth()->check('admin_reviews'))
        ) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.listingfields.' . $group->slug . '.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $fields = \App\Models\ListingField::search(null, [], 'admin/' . $group->slug . '-fields/' . $type->slug)
            ->where('listingfieldgroup_id', $group->id)
            ->where('type_id', $type->id)
            ->orderBy('weight')
            ->paginate();

        $table = dataTable($fields)
            ->addColumns([
                'label' => [__('admin.listingfields.datatable.label.label')],
                'type' => [__('admin.listingfields.datatable.label.type'), function ($field) {
                    return $this->types[$field->type];
                }],
            ])
            ->addActions([
                'edit' => [__('admin.listingfields.datatable.action.edit'), function ($field) use ($group, $type) {
                    return adminRoute($group->slug . '-fields/' . $type->slug . '/update/' . $field->id);
                }],
                'constraints' => [__('admin.listingfields.datatable.action.constraints'), function ($field) use ($group, $type) {
                    return adminRoute($group->slug . '-field-constraints/' . $type->slug, ['field_id' => $field->id]);
                }],
                'options' => [__('admin.listingfields.datatable.action.options'), function ($field) use ($group, $type) {
                    if (in_array($field->type, ['checkbox', 'mselect', 'select', 'radio'])) {
                        return adminRoute($group->slug . '-field-options/' . $type->slug, ['field_id' => $field->id]);
                    }
                }],
                'delete' => [__('admin.listingfields.datatable.action.delete'), function ($field) use ($group, $type) {
                    if (null !== $field->customizable && null !== $field->removable) {
                        return adminRoute($group->slug . '-fields/' . $type->slug . '/delete/' . $field->id);
                    }
                }],
            ])
            ->setSortable('listing-fields');

        return response(layout()->content(
            view('admin/listing-fields/index', [
                'group' => $group,
                'type' => $type,
                'fields' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (false === auth()->check(['admin_content', 'admin_fields'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\ListingFieldGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (
            ('listings' == $group->slug && false === auth()->check('admin_listings')) || 
            ('messages' == $group->slug && false === auth()->check('admin_messages')) ||
            ('reviews' == $group->slug && false === auth()->check('admin_reviews'))
        ) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.listingfields.' . $group->slug . '.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $field = new \App\Models\ListingField();

        $field->type_id = $type->id;
        
        layout()->addFooterJs('<script>
            $(document).ready(function() {
                var rangeElements = $(\'input[id^="range_"]\').closest(\'div[class*="form-group"]\');                    
                var uploadidElement = $(\'#upload_id\').closest(\'div[class*="form-group"]\');                    
                var socialidElement = $(\'#socialprofiletype_id\').closest(\'div[class*="form-group"]\');                    

                if ($.inArray($("#type").val(), ["dropzone"]) === -1) {
                    uploadidElement.hide();
                }

                if ($("#search_type").val() != "range") {
                    rangeElements.hide();
                }

                if ($.inArray($("#type").val(), ["social"]) === -1) {
                    socialidElement.hide();
                }

                $("#type").on("change", function() {
                    if ($.inArray($(this).val(), ["dropzone"]) === -1) {
                        uploadidElement.slideUp("slow");
                    } else {
                        uploadidElement.slideDown("slow");
                    }

                    if ($.inArray($(this).val(), ["social"]) === -1) {
                        socialidElement.slideUp("slow");
                    } else {
                        socialidElement.slideDown("slow");
                    }
                });

                $("#search_type").on("change", function() {                  
                    if ($("#search_type").val() == "range") {
                        rangeElements.slideDown("slow");
                    } else {
                        rangeElements.slideUp("slow");
                    }
                });
            });
        </script>
        ');

        unset(
            $this->types['locationmappicker'], 
            $this->types['password'], 
            $this->types['ro'], 
            $this->types['hidden']
        );

        $form = $this->getForm($field)
            ->remove('value')
            ->add('submit', 'submit', ['label' => __('admin.listingfields.form.label.submit')]);

        if (in_array($group->slug, ['messages', 'reviews'])) {
            $form
                ->remove('submittable')
                ->remove('updatable')
                ->remove('queryable')
                ->remove('search_type')
                ->remove('range_min')
                ->remove('range_max')
                ->remove('range_step');        
        }

        $form->handleRequest();
    
        if ($form->isSubmitted()) {
            $input = $form->getValues();
            
            if ($form->isValid()) {
                if (\App\Models\ListingField::where('name', 'custom_' . $input->name)->where('type_id', $type->id)->count() > 0) {
                    $form->setValidationError('name', __('form.validation.unique'));
                }
            }

            if ($form->isValid()) {
                $field->name = 'custom_' . $input->name;
                $field->customizable = 1;
                $field->removable = 1;
                $group->fields()->save($field);

                $field->categories()->attach($input->categories ?? []);
                $field->products()->attach($input->products ?? []);

                if (null !== $input->required) {
                    $constraint = new \App\Models\ListingFieldConstraint(['name' => ($input->type == 'dropzone' ? 'filerequired' : 'required')]);
                    $constraint->customizable = 1;
                    $field->constraints()->save($constraint);
                }

                return redirect(adminRoute($group->slug . '-fields/' . $type->slug, session()->get('admin/' . $group->slug . '-fields/' . $type->slug)))
                    ->with('success', view('flash/success', ['message' => __('admin.listingfields.alert.create.success', ['name' => $field->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/listing-fields/create', [
                'group' => $group,
                'type' => $type,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (false === auth()->check(['admin_content', 'admin_fields'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\ListingFieldGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (
            ('listings' == $group->slug && false === auth()->check('admin_listings')) || 
            ('messages' == $group->slug && false === auth()->check('admin_messages')) ||
            ('reviews' == $group->slug && false === auth()->check('admin_reviews'))
        ) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $field = $type->fields()->where('id', $params['id'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.listingfields.' . $group->slug . '.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        layout()->addFooterJs('<script>
            $(document).ready(function() {
                var rangeElements = $(\'input[id^="range_"]\').closest(\'div[class*="form-group"]\');

                if ($("#search_type").val() != "range") {
                    rangeElements.hide();
                }

                $("#search_type").on("change", function() {                  
                    if ($("#search_type").val() == "range") {
                        rangeElements.slideDown("slow");
                    } else {
                        rangeElements.slideUp("slow");
                    }
                });
            });
        </script>
        ');

        $form = $this->getForm($field);

        $form->remove('required');
        $form->remove('type');
        $form->remove('name');

        if ($field->type != 'dropzone') {
            $form->remove('upload_id');
        }

        if ($field->type != 'social') {
            $form->remove('socialprofiletype_id');
        }

        if (null === $field->customizable || null === $field->removable) {
            $form
                ->remove('submittable')
                ->remove('updatable')
                ->remove('queryable')
                ->remove('outputable')
                ->remove('outputable_search')
                ->remove('upload_id')
                ->remove('search_type')
                ->remove('range_min')
                ->remove('range_max')
                ->remove('range_step');
        }

        if (in_array($group->slug, ['messages', 'reviews'])) {
            $form
                ->remove('submittable')
                ->remove('updatable')
                ->remove('queryable')
                ->remove('search_type')
                ->remove('range_min')
                ->remove('range_max')
                ->remove('range_step');        
        }

        $form
            ->add('submit', 'submit', ['label' => __('admin.listingfields.form.label.update')])
            ->setValue('products', $field->products->pluck('id')->all())
            ->setValue('categories', $field->categories->pluck('id')->all())
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();
            
            if ($form->isValid()) {
                $field->save();

                $field->categories()->sync($input->categories);
                $field->products()->sync($input->products);

                return redirect(adminRoute($group->slug . '-fields/' . $type->slug, session()->get('admin/' . $group->slug . '-fields/' . $type->slug)))
                    ->with('success', view('flash/success', ['message' => __('admin.listingfields.alert.update.success', ['name' => $field->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/listing-fields/update', [
                'group' => $group,
                'type' => $type,
                'field' => $field,
                'form' => $form,
                'alert' => $alert ?? null,
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (false === auth()->check(['admin_content', 'admin_fields'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\ListingFieldGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (
            ('listings' == $group->slug && false === auth()->check('admin_listings')) || 
            ('messages' == $group->slug && false === auth()->check('admin_messages')) ||
            ('reviews' == $group->slug && false === auth()->check('admin_reviews'))
        ) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $field = $type->fields()->where('id', $params['id'])->whereNotNull('removable')->whereNotNull('customizable')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $field->delete();

        return redirect(adminRoute($group->slug . '-fields/' . $type->slug, session()->get('admin/' . $group->slug . '-fields/' . $type->slug)))
            ->with('success', view('flash/success', ['message' => __('admin.listingfields.alert.remove.success', ['name' => $field->name])]));
    }


    private function getForm($model)
    {
        $form = form($model)
            ->add('submittable', 'toggle', ['label' => __('admin.listingfields.form.label.submittable'), 'value' => '1'])
            ->add('updatable', 'toggle', ['label' => __('admin.listingfields.form.label.updatable'), 'value' => '1'])
            ->add('queryable', 'toggle', ['label' => __('admin.listingfields.form.label.queryable')])
            ->add('outputable', 'toggle', ['label' => __('admin.listingfields.form.label.outputable'), 'value' => '1'])
            ->add('outputable_search', 'toggle', ['label' => __('admin.listingfields.form.label.outputable_search'), 'value' => '1'])
            ->add('required', 'toggle', ['label' => __('admin.listingfields.form.label.required')])
            ->add('type', 'select', ['label' => __('admin.listingfields.form.label.type'), 'options' => $this->types])
            ->add('icon', 'icon', ['label' => __('admin.listingfields.form.label.icon')])
            ->add('upload_id', 'select', ['label' => __('admin.listingfields.form.label.upload_type'), 'options' => \App\Models\UploadType::whereNotNull('public')->get()->pluck('name', 'id')->all()])
            ->add('socialprofiletype_id', 'select', ['label' => __('admin.listingfields.form.label.social_profile_type'), 'options' => \App\Models\SocialProfileType::all()->pluck('name', 'id')->all()])
            ->add('search_type', 'select', ['label' => __('admin.listingfields.form.label.search_type'), 'options' => ['eq' => __('admin.listingfields.form.label.search_type.eq'), 'like' => __('admin.listingfields.form.label.search_type.like'), 'range' => __('admin.listingfields.form.label.search_type.range')]])
            ->add('range_min', 'number', ['label' => __('admin.listingfields.form.label.range_min'), 'value' => 1, 'constraints' => 'required'])
            ->add('range_max', 'number', ['label' => __('admin.listingfields.form.label.range_max'), 'value' => 100, 'constraints' => 'required'])
            ->add('range_step', 'number', ['label' => __('admin.listingfields.form.label.range_step'), 'value' => 10, 'constraints' => 'required'])
            ->add('label', 'translatable', ['label' => __('admin.listingfields.form.label.label')])
            ->add('name', 'text', ['label' => __('admin.listingfields.form.label.name'), 'constraints' => 'required|alphanumeric|maxlength:120']);

        switch ($model->type) {
            case 'checkbox':
            case 'mselect':
                $form->add('value', 'textarea', ['label' => __('admin.listingfields.form.label.value')]);
                break;
            case 'hidden':
            case 'password':
            case 'radio':
            case 'ro':
            case 'select':
                $form->add('value', 'text', ['label' => __('admin.listingfields.form.label.value')]);
                break;
            case 'color':
            case 'date':
            case 'dates':
            case 'datetime':
            case 'email':
            case 'htmltextarea':
            case 'keywords':
            case 'number':
            case 'phone':
            case 'price':
            case 'rating':
            case 'social':
            case 'text':
            case 'textarea':
            case 'time':
            case 'timezone':
            case 'toggle':
            case 'url':
            case 'youtube':
                $form->add('value', $model->type, ['label' => __('admin.listingfields.form.label.value')]);
                break;
            default:
        }

        $form
            ->add('placeholder', 'translatable', ['label' => __('admin.listingfields.form.label.placeholder')])
            ->add('description', 'translatable', ['label' => __('admin.listingfields.form.label.description')])
            ->add('products', 'tree', [
                'label' => __('admin.listingfields.form.label.products'), 
                'tree_source' => (new \App\Models\Product())->getTreeWithHidden($model->type_id),
            ])
            ->add('categories', 'tree', [
                'label' => __('admin.listingfields.form.label.categories'), 
                'tree_source' => (new \App\Models\Category())->getExpandedTree($model->type_id),
            ])
            ->add('schema_itemprop', 'text', ['label' => __('admin.listingfields.form.label.schema_itemprop')]);

        return $form;
    }

}
