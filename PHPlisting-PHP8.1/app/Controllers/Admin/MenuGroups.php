<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class MenuGroups
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.menugroups.title.index'));

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'asc';
        }

        $groups = \App\Models\WidgetMenuGroup::search(null, [], 'admin/menu-groups')
            ->paginate();

        $table = dataTable($groups)
            ->addColumns([
                'id' => [__('admin.menugroups.datatable.label.id')],
                'name' => [__('admin.menugroups.datatable.label.name')],
            ])
            ->orderColumns([
                'id',
                'name',
            ])
            ->addActions([
                'edit' => [__('admin.menugroups.datatable.action.edit'), function ($group) {
                    return adminRoute('menu-groups/update/' . $group->id);
                }],
                'items' => [__('admin.menugroups.datatable.action.items'), function ($group) {
                    return adminRoute('menu/' . $group->id);
                }],
                'delete' => [__('admin.menugroups.datatable.action.delete'), function ($group) {
                    if (null !== $group->customizable) {
                        return adminRoute('menu-groups/delete/' . $group->id);
                    }
                }],
            ]);

        return response(layout()->content(
            view('admin/menu-groups/index', [
                'menugroups' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.menugroups.title.create'));

        $group = new \App\Models\WidgetMenuGroup();

        $form = $this->getForm($group)
            ->add('submit', 'submit', ['label' => __('admin.menugroups.form.label.submit')])
            ->handleRequest();
    
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $group->customizable = 1;
                $group->save();

                return redirect(adminRoute('menu-groups', session()->get('admin/menu-groups')))
                    ->with('success', view('flash/success', ['message' => __('admin.menugroups.alert.create.success')]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/menu-groups/create', [
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

        if (null === $group = \App\Models\WidgetMenuGroup::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.menugroups.title.update'));

        $form = $this->getForm($group)
            ->add('submit', 'submit', ['label' => __('admin.menugroups.form.label.update')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $group->save();

                return redirect(adminRoute('menu-groups', session()->get('admin/menu-groups')))
                    ->with('success', view('flash/success', ['message' => __('admin.menugroups.alert.update.success')]));
            } else {
                $message = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/menu-groups/update', [
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

        if (null === $group = \App\Models\WidgetMenuGroup::where('id', $params['id'])->whereNotNull('customizable')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $group->delete();

        return redirect(adminRoute('menu-groups', session()->get('admin/menu-groups')))
            ->with('success', view('flash/success', ['message' => __('admin.menugroups.alert.remove.success')]));
    }


    private function getForm($model)
    {
        return form($model)
            ->add('name', 'text', ['label' => __('admin.menugroups.form.label.name'), 'constraints' => 'required']);
    }

}
