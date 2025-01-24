<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class WidgetFieldConstraints
    extends \App\Controllers\Admin\BaseController
{

    public $types;

    public function __construct()
    {
        parent::__construct();

        $this->types = [
            'alphanumericdashslash' => __('admin.widgetfieldconstraints.type.alphanumericdashslash'),
            'alphanumericdashspace' => __('admin.widgetfieldconstraints.type.alphanumericdashspace'),
            'alphanumericdash' => __('admin.widgetfieldconstraints.type.alphanumericdash'),
            'alphanumeric' => __('admin.widgetfieldconstraints.type.alphanumeric'),
            'alpha' => __('admin.widgetfieldconstraints.type.alpha'),
            'array' => __('admin.widgetfieldconstraints.type.array'),
            'bannedwords' => __('admin.widgetfieldconstraints.type.bannedwords'),
            'equalto' => __('admin.widgetfieldconstraints.type.equalto'),
            'filerequired' => __('admin.widgetfieldconstraints.type.filerequired'),
            'ip' => __('admin.widgetfieldconstraints.type.ip'),
            'length' => __('admin.widgetfieldconstraints.type.length'),
            'maxlength' => __('admin.widgetfieldconstraints.type.maxlength'),
            'max' => __('admin.widgetfieldconstraints.type.max'),
            'minlength' => __('admin.widgetfieldconstraints.type.minlength'),
            'min' => __('admin.widgetfieldconstraints.type.min'),
            'notequalto' => __('admin.widgetfieldconstraints.type.notequalto'),
            'number' => __('admin.widgetfieldconstraints.type.number'),
            'price' => __('admin.widgetfieldconstraints.type.price'),
            'required' => __('admin.widgetfieldconstraints.type.required'),
            'string' => __('admin.widgetfieldconstraints.type.string'),
            'unique' => __('admin.widgetfieldconstraints.type.unique'),
        ];
    }

    public function actionIndex($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\WidgetFieldGroup::find($params['group'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === request()->get->get('field_id') || null === $field = $group->fields()->where('id', request()->get->get('field_id'))->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.widgetfieldconstraints.title.index'));

        $constraints = $field->constraints()
            ->orderBy('weight')
            ->paginate();

        $table = dataTable($constraints)
            ->addColumns([
                'name' => [__('admin.widgetfieldconstraints.datatable.label.name'), function ($constraint) {
                    return $this->types[$constraint->name];
                }],
            ])
            ->addActions([
                'edit' => [__('admin.widgetfieldconstraints.datatable.action.edit'), function ($constraint) use ($group, $field) {
                    return adminRoute('widget-field-constraints/' . $group->id . '/update/' . $constraint->id, ['field_id' => $field->id]);
                }],
                'delete' => [__('admin.widgetfieldconstraints.datatable.action.delete'), function ($constraint) use ($group, $field) {
                    return adminRoute('widget-field-constraints/' . $group->id . '/delete/' . $constraint->id, ['field_id' => $field->id]);
                }],
            ])
            ->setSortable('widget-field-constraints');

        return response(layout()->content(
            view('admin/widget-field-constraints/index', [
                'group' => $group,
                'field' => $field,
                'constraints' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\WidgetFieldGroup::find($params['group'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === request()->get->get('field_id') || null === $field = $group->fields()->where('id', request()->get->get('field_id'))->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.widgetfieldconstraints.title.create'));

        $constraint = new \App\Models\WidgetFieldConstraint();

        $form = form($constraint)
            ->add('name', 'select', ['label' => __('admin.widgetfieldconstraints.form.label.name'), 'options' => $this->types, 'constraints' => 'required'])
            ->add('value', 'text', ['label' => __('admin.widgetfieldconstraints.form.label.value')])
            ->add('submit', 'submit', ['label' => __('admin.widgetfieldconstraints.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {           

            if ($form->isValid()) {
                $field->constraints()->save($constraint);

                return redirect(adminRoute('widget-field-constraints/' . $group->id, ['field_id' => $field->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.widgetfieldconstraints.alert.create.success', ['name' => $constraint->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/widget-field-constraints/create', [
                'group' => $group,
                'field' => $field,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\WidgetFieldGroup::find($params['group'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === request()->get->get('field_id') || null === $field = $group->fields()->where('id', request()->get->get('field_id'))->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $constraint = \App\Models\WidgetFieldConstraint::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.widgetfieldconstraints.title.create'));

        $form = form($constraint)
            ->add('name', 'select', ['label' => __('admin.widgetfieldconstraints.form.label.name'), 'options' => $this->types, 'constraints' => 'required'])
            ->add('value', 'text', ['label' => __('admin.widgetfieldconstraints.form.label.value')])
            ->add('submit', 'submit', ['label' => __('admin.widgetfieldconstraints.form.label.update')])
            ->handleRequest();

        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $field->constraints()->save($constraint);

                return redirect(adminRoute('widget-field-constraints/' . $group->id, ['field_id' => $field->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.widgetfieldconstraints.alert.update.success', ['name' => $constraint->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/widget-field-constraints/update', [
                'group' => $group,
                'field' => $field,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\WidgetFieldGroup::find($params['group'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === request()->get->get('field_id') || null === $field = $group->fields()->where('id', request()->get->get('field_id'))->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $constraint = \App\Models\WidgetFieldConstraint::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $constraint->delete();

        return redirect(adminRoute('widget-field-constraints/' . $group->id, ['field_id' => $field->id]))
            ->with('success', view('flash/success', ['message' => __('admin.widgetfieldconstraints.alert.remove.success', ['name' => $constraint->name])]));
    }

}
