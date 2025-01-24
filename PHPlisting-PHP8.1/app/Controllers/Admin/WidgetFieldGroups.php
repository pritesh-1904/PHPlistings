<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class WidgetFieldGroups
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.widgetfieldgroups.title.index'));

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'asc';
        }

        $groups = \App\Models\WidgetFieldGroup::search(null, [], 'admin/widget-field-groups')
            ->paginate();

        $table = dataTable($groups)
            ->addColumns([
                'id' => [__('admin.widgetfieldgroups.datatable.label.id')],
                'name' => [__('admin.widgetfieldgroups.datatable.label.name')],
            ])
            ->orderColumns([
                'id',
                'name',
            ])
            ->addActions([
                'edit' => [__('admin.widgetfieldgroups.datatable.action.edit'), function ($group) {
                    return adminRoute('widget-field-groups/update/' . $group->id);
                }],
                'fields' => [__('admin.widgetfieldgroups.datatable.action.fields'), function ($group) {
                    return adminRoute('widget-fields/' . $group->id);
                }],
                'delete' => [__('admin.widgetfieldgroups.datatable.action.delete'), function ($group) {
                    if (null !== $group->customizable) {
                        return adminRoute('widget-field-groups/delete/' . $group->id);
                    }
                }],
            ]);

        return response(layout()->content(
            view('admin/widget-field-groups/index', [
                'groups' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.widgetfieldgroups.title.create'));

        $group = new \App\Models\WidgetFieldGroup();

        $form = $this->getForm($group)
            ->add('submit', 'submit', ['label' => __('admin.widgetfieldgroups.form.label.submit')])
            ->handleRequest();
    
        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                $group->customizable = 1;
                $group->slug = slugify($input->name);
                $group->save();

                return redirect(adminRoute('widget-field-groups', session()->get('admin/widget-field-groups')))
                    ->with('success', view('flash/success', ['message' => __('admin.widgetfieldgroups.alert.create.success', ['name' => $group->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/widget-field-groups/create', [
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

        if (null === $group = \App\Models\WidgetFieldGroup::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.widgetfieldgroups.title.update'));

        $form = $this->getForm($group)
            ->add('submit', 'submit', ['label' => __('admin.widgetfieldgroups.form.label.update')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                $group->slug = slugify($input->name);
                $group->save();

                return redirect(adminRoute('widget-field-groups', session()->get('admin/widget-field-groups')))
                    ->with('success', view('flash/success', ['message' => __('admin.widgetfieldgroups.alert.update.success', ['name' => $group->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/widget-field-groups/update', [
                'group' => $group,
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

        if (null === $group = \App\Models\WidgetFieldGroup::where('id', $params['id'])->whereNotNull('customizable')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $group->delete();

        return redirect(adminRoute('widget-field-groups', session()->get('admin/widget-field-groups')))
            ->with('success', view('flash/success', ['message' => __('admin.widgetfieldgroups.alert.remove.success', ['name' => $group->name])]));
    }

    private function getForm($model)
    {
        return form($model)->add('name', 'text', ['label' => __('admin.widgetfieldgroups.form.label.name'), 'constraints' => 'required']);
    }

}
