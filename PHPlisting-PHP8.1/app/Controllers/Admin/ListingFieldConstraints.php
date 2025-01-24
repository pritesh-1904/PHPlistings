<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class ListingFieldConstraints
    extends \App\Controllers\Admin\BaseController
{
    public $types;

    public function __construct()
    {
        parent::__construct();

        $this->types = [
            'alphanumericdashslash' => __('admin.listingfieldconstraints.type.alphanumericdashslash'),
            'alphanumericdashspace' => __('admin.listingfieldconstraints.type.alphanumericdashspace'),
            'alphanumericdash' => __('admin.listingfieldconstraints.type.alphanumericdash'),
            'alphanumeric' => __('admin.listingfieldconstraints.type.alphanumeric'),
            'alpha' => __('admin.listingfieldconstraints.type.alpha'),
            'array' => __('admin.listingfieldconstraints.type.array'),
            'bannedwords' => __('admin.listingfieldconstraints.type.bannedwords'),
            'equalto' => __('admin.listingfieldconstraints.type.equalto'),
            'filerequired' => __('admin.listingfieldconstraints.type.filerequired'),
            'ip' => __('admin.listingfieldconstraints.type.ip'),
            'length' => __('admin.listingfieldconstraints.type.length'),
            'maxlength' => __('admin.listingfieldconstraints.type.maxlength'),
            'max' => __('admin.listingfieldconstraints.type.max'),
            'minlength' => __('admin.listingfieldconstraints.type.minlength'),
            'min' => __('admin.listingfieldconstraints.type.min'),
            'notequalto' => __('admin.listingfieldconstraints.type.notequalto'),
            'number' => __('admin.listingfieldconstraints.type.number'),
            'price' => __('admin.listingfieldconstraints.type.price'),
            'required' => __('admin.listingfieldconstraints.type.required'),
            'string' => __('admin.listingfieldconstraints.type.string'),
            'unique' => __('admin.listingfieldconstraints.type.unique'),
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

        if (!isset(request()->get->field_id) || null === $field = $type->fields()->where('id', request()->get->field_id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.listingfieldconstraints.' . $group->slug . '.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $constraints = $field->constraints()
            ->orderBy('weight')
            ->paginate();

        $table = dataTable($constraints)
            ->addColumns([
                'name' => [__('admin.listingfieldconstraints.datatable.label.name'), function ($constraint) {
                    return $this->types[$constraint->name];
                }],
            ])
            ->addActions([
                'edit' => [__('admin.listingfieldconstraints.datatable.action.edit'), function ($constraint) use ($group, $type, $field) {
                    if (null === $constraint->customizable) {
                        return null;
                    }

                    return adminRoute($group->slug . '-field-constraints/' . $type->slug . '/update/' . $constraint->id, ['field_id' => $field->id]);
                }],
                'delete' => [__('admin.listingfieldconstraints.datatable.action.delete'), function ($constraint) use ($group, $type, $field) {
                    if (null === $constraint->customizable) {
                        return null;
                    }

                    return adminRoute($group->slug . '-field-constraints/' . $type->slug . '/delete/' . $constraint->id, ['field_id' => $field->id]);
                }],
            ])
            ->setSortable('listing-field-constraints');

        return response(layout()->content(
            view('admin/listing-field-constraints/index', [
                'group' => $group,
                'type' => $type,
                'field' => $field,
                'constraints' => $table,
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

        if (null === request()->get->get('field_id') || null === $field = $type->fields()->where('id', request()->get->field_id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.listingfieldconstraints.' . $group->slug . '.title.create', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $constraint = new \App\Models\ListingFieldConstraint();

        $form = form($constraint)
            ->add('name', 'select', ['label' => __('admin.listingfieldconstraints.form.label.name'), 'options' => $this->types, 'constraints' => 'required'])
            ->add('value', 'text', ['label' => __('admin.listingfieldconstraints.form.label.value')])
            ->add('submit', 'submit', ['label' => __('admin.listingfieldconstraints.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {           
            $input = $form->getValues();

            if ($form->isValid()) {
                $constraint->customizable = 1;
                
                $field->constraints()->save($constraint);

                return redirect(adminRoute($group->slug . '-field-constraints/' . $type->slug, ['field_id' => $field->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.listingfieldconstraints.alert.create.success', ['name' => $constraint->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/listing-field-constraints/create', [
                'group' => $group,
                'type' => $type,
                'field' => $field,
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

        if (null === request()->get->get('field_id') || null === $field = $type->fields()->where('id', request()->get->field_id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $constraint = \App\Models\ListingFieldConstraint::where('id', $params['id'])->whereNotNull('customizable')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.listingfieldconstraints.' . $group->slug . '.title.create', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $form = form($constraint)
            ->add('name', 'select', ['label' => __('admin.listingfieldconstraints.form.label.name'), 'options' => $this->types, 'constraints' => 'required'])
            ->add('value', 'text', ['label' => __('admin.listingfieldconstraints.form.label.value')])
            ->add('submit', 'submit', ['label' => __('admin.listingfieldconstraints.form.label.update')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $field->constraints()->save($constraint);

                return redirect(adminRoute($group->slug . '-field-constraints/' . $type->slug, ['field_id' => $field->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.listingfieldconstraints.alert.update.success', ['name' => $constraint->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/listing-field-constraints/update', [
                'group' => $group,
                'type' => $type,
                'field' => $field,
                'constraint' => $constraint,
                'form' => $form,
                'alert' => $alert ?? null
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

        if (null === request()->get->get('field_id') || null === $field = $type->fields()->where('id', request()->get->field_id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $constraint = \App\Models\ListingFieldConstraint::where('id', $params['id'])->whereNotNull('customizable')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $constraint->delete();

        return redirect(adminRoute($group->slug . '-field-constraints/' . $type->slug, ['field_id' => $field->id]))
            ->with('success', view('flash/success', ['message' => __('admin.listingfieldconstraints.alert.remove.success', ['name' => $constraint->name])]));
    }

}
