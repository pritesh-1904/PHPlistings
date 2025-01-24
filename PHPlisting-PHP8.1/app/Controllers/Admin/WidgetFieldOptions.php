<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class WidgetFieldOptions
    extends \App\Controllers\Admin\BaseController
{

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

        layout()->setTitle(__('admin.widgetfieldoptions.title.index'));

        $options = $field->options()
            ->orderBy('weight')
            ->paginate();

        $table = dataTable($options)
            ->addColumns([
                'name' => [__('admin.widgetfieldoptions.datatable.label.name')],
                'value' => [__('admin.widgetfieldoptions.datatable.label.value')],
            ])
            ->addActions([
                'edit' => [__('admin.widgetfieldoptions.datatable.action.edit'), function ($option) use ($group, $field) {
                    return adminRoute('widget-field-options/' . $group->id . '/update/' . $option->id, ['field_id' => $field->id]);
                }],
                'delete' => [__('admin.widgetfieldoptions.datatable.action.delete'), function ($option) use ($group, $field) {
                    return adminRoute('widget-field-options/' . $group->id . '/delete/' . $option->id, ['field_id' => $field->id]);
                }],
            ])
            ->setSortable('widget-field-options');

        return response(layout()->content(
            view('admin/widget-field-options/index', [
                'group' => $group,
                'field' => $field,
                'options' => $table,
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

        layout()->setTitle(__('admin.widgetfieldoptions.title.create'));

        $option = new \App\Models\WidgetFieldOption();

        $form = form($option)
            ->add('name', 'text', ['label' => __('admin.widgetfieldoptions.form.label.name'), 'constraints' => 'required|alphanumeric'])
            ->add('value', 'translatable', ['label' => __('admin.widgetfieldoptions.form.label.value'), 'constraints' => 'transrequired'])
            ->add('submit', 'submit', ['label' => __('admin.widgetfieldoptions.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {           

            if ($form->isValid()) {
                $field->options()->save($option);

                return redirect(adminRoute('widget-field-options/' . $group->id, ['field_id' => $field->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.widgetfieldoptions.alert.create.success', ['name' => $option->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/widget-field-options/create', [
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

        if (null === $option = \App\Models\WidgetFieldOption::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.widgetfieldoptions.title.create'));

        $form = form($option)
            ->add('name', 'text', ['label' => __('admin.widgetfieldoptions.form.label.name'), 'constraints' => 'required|alphanumeric'])
            ->add('value', 'translatable', ['label' => __('admin.widgetfieldoptions.form.label.value'), 'constraints' => 'transrequired'])
            ->add('submit', 'submit', ['label' => __('admin.widgetfieldoptions.form.label.update')])
            ->handleRequest();

        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $option->save();

                return redirect(adminRoute('widget-field-options/' . $group->id, ['field_id' => $field->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.widgetfieldoptions.alert.update.success', ['name' => $option->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/widget-field-options/update', [
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

        if (null === $option = \App\Models\WidgetFieldOption::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $option->delete();

        return redirect(adminRoute('widget-field-options/' . $group->id, ['field_id' => $field->id]))
            ->with('success', view('flash/success', ['message' => __('admin.widgetfieldoptions.alert.remove.success', ['name' => $option->name])]));
    }

}
