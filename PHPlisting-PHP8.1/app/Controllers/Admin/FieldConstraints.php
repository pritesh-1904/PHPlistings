<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class FieldConstraints
    extends \App\Controllers\Admin\BaseController
{

    public $types;

    public function __construct()
    {
        parent::__construct();

        $this->types = [
            'alphanumericdashslash' => __('admin.fieldconstraints.type.alphanumericdashslash'),
            'alphanumericdashspace' => __('admin.fieldconstraints.type.alphanumericdashspace'),
            'alphanumericdash' => __('admin.fieldconstraints.type.alphanumericdash'),
            'alphanumeric' => __('admin.fieldconstraints.type.alphanumeric'),
            'alpha' => __('admin.fieldconstraints.type.alpha'),
            'array' => __('admin.fieldconstraints.type.array'),
            'bannedwords' => __('admin.fieldconstraints.type.bannedwords'),
            'equalto' => __('admin.fieldconstraints.type.equalto'),
            'filerequired' => __('admin.fieldconstraints.type.filerequired'),
            'ip' => __('admin.fieldconstraints.type.ip'),
            'length' => __('admin.fieldconstraints.type.length'),
            'maxlength' => __('admin.fieldconstraints.type.maxlength'),
            'max' => __('admin.fieldconstraints.type.max'),
            'minlength' => __('admin.fieldconstraints.type.minlength'),
            'min' => __('admin.fieldconstraints.type.min'),
            'notequalto' => __('admin.fieldconstraints.type.notequalto'),
            'number' => __('admin.fieldconstraints.type.number'),
            'price' => __('admin.fieldconstraints.type.price'),
            'required' => __('admin.fieldconstraints.type.required'),
            'string' => __('admin.fieldconstraints.type.string'),
            'unique' => __('admin.fieldconstraints.type.unique'),
        ];
    }

    public function actionIndex($params)
    {
        if (!auth()->check('admin_fields')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\FieldGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ('users' == $group->slug && !auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === request()->get->get('field_id') || null === $field = $group->fields()->where('id', request()->get->field_id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.fieldconstraints.' . $group->slug . '.title.index'));

        $constraints = $field
            ->constraints()
            ->orderBy('weight')
            ->paginate();

        $table = dataTable($constraints)
            ->addColumns([
                'name' => [__('admin.fieldconstraints.datatable.label.name'), function ($constraint) {
                    return $this->types[$constraint->name] ?? $constraint->name;
                }],
            ])
            ->addActions([
                'edit' => [__('admin.fieldconstraints.datatable.action.edit'), function ($constraint) use ($group, $field) {
                    if (null === $constraint->customizable) {
                        return null;
                    }

                    return adminRoute('field-constraints/' . $group->slug . '/update/' . $constraint->id, ['field_id' => $field->id]);
                }],
                'delete' => [__('admin.fieldconstraints.datatable.action.delete'), function ($constraint) use ($group, $field) {
                    if (null === $constraint->customizable) {
                        return null;
                    }

                    return adminRoute('field-constraints/' . $group->slug . '/delete/' . $constraint->id, ['field_id' => $field->id]);
                }],
            ])
            ->setSortable('field-constraints');

        return response(layout()->content(
            view('admin/field-constraints/index', [
                'group' => $group,
                'field' => $field,
                'constraints' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_fields')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\FieldGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ('users' == $group->slug && !auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === request()->get->get('field_id') || null === $field = $group->fields()->where('id', request()->get->field_id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.fieldconstraints.' . $group->slug . '.title.create'));

        $constraint = new \App\Models\FieldConstraint();

        $form = form($constraint)
            ->add('name', 'select', ['label' => __('admin.fieldconstraints.form.label.name'), 'options' => $this->types, 'constraints' => 'required'])
            ->add('value', 'text', ['label' => __('admin.fieldconstraints.form.label.value')])
            ->add('submit', 'submit', ['label' => __('admin.fieldconstraints.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {           
            if ($form->isValid()) {
                $constraint->customizable = 1;

                $field->constraints()->save($constraint);

                return redirect(adminRoute('field-constraints/' . $group->slug, ['field_id' => $field->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.fieldconstraints.alert.create.success', ['name' => $constraint->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/field-constraints/create', [
                'group' => $group,
                'field' => $field,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (!auth()->check('admin_fields')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\FieldGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ('users' == $group->slug && !auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === request()->get->get('field_id') || null === $field = $group->fields()->where('id', request()->get->field_id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $constraint = $field->constraints()->where('id', $params['id'])->whereNotNull('customizable')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.fieldconstraints.' . $group->slug . '.title.update'));

        $form = form($constraint)
            ->add('name', 'select', ['label' => __('admin.fieldconstraints.form.label.name'), 'options' => $this->types, 'constraints' => 'required'])
            ->add('value', 'text', ['label' => __('admin.fieldconstraints.form.label.value')])
            ->add('submit', 'submit', ['label' => __('admin.fieldconstraints.form.label.update')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $field->constraints()->save($constraint);

                return redirect(adminRoute('field-constraints/' . $group->slug, ['field_id' => $field->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.fieldconstraints.alert.update.success', ['name' => $constraint->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/field-constraints/update', [
                'group' => $group,
                'field' => $field,
                'constraint' => $constraint,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (!auth()->check('admin_fields')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\FieldGroup::where('slug', $params['group'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ('users' == $group->slug && !auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === request()->get->get('field_id') || null === $field = $group->fields()->where('id', request()->get->field_id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $constraint = $field->constraints()->where('id', $params['id'])->whereNotNull('customizable')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $constraint->delete();

        return redirect(adminRoute('field-constraints/' . $group->slug, ['field_id' => $field->id]))
            ->with('success', view('flash/success', ['message' => __('admin.fieldconstraints.alert.remove.success', ['name' => $constraint->name])]));
    }

}
