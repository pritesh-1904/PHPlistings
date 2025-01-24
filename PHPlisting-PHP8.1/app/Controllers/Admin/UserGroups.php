<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class UserGroups
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.usergroups.title.index'));

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'asc';
        }

        $groups = \App\Models\UserGroup::search(null, [], 'admin/user-groups')
            ->paginate();

        $table = dataTable($groups)
            ->addColumns([
                'id' => [__('admin.usergroups.datatable.label.id')],
                'name' => [__('admin.usergroups.datatable.label.name')],
            ])
            ->orderColumns([
                'id',
                'name',
            ])
            ->addActions([
                'edit' => [__('admin.usergroups.datatable.action.edit'), function ($group) {
                    return adminRoute('user-groups/update/' . $group->id);
                }],
                'delete' => [__('admin.usergroups.datatable.action.delete'), function ($group) {
                    if (null !== $group->customizable) {
                        return adminRoute('user-groups/delete/' . $group->id);
                    }
                }],
            ]);

        return response(layout()->content(
            view('admin/user-groups/index', [
                'groups' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.usergroups.title.create'));

        $group = new \App\Models\UserGroup();

        $form = $this->getForm($group)
            ->add('submit', 'submit', ['label' => __('admin.usergroups.form.label.submit')])
            ->handleRequest();
    
        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $group->customizable = 1;
                $group->save();

                $group->roles()->attach($form->getValues()->roles);

                return redirect(adminRoute('user-groups', session()->get('admin/user-groups')))
                    ->with('success', view('flash/success', ['message' => __('admin.usergroups.alert.create.success', ['name' => $group->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/user-groups/create', [
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (!auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\UserGroup::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.usergroups.title.update'));

        $form = $this->getForm($group)
            ->add('submit', 'submit', ['label' => __('admin.usergroups.form.label.update')])
            ->setValue('roles', $group->roles->pluck('id')->all())
            ->handleRequest();

        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $group->save();

                $group->roles()->sync($form->getValues()->roles);

                return redirect(adminRoute('user-groups', session()->get('admin/user-groups')))
                    ->with('success', view('flash/success', ['message' => __('admin.usergroups.alert.update.success', ['name' => $group->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/user-groups/update', [
                'form' => $form,
                'message' => $message ?? null
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (!auth()->check('admin_users')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\UserGroup::where('id', $params['id'])->whereNotNull('customizable')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $group->delete();

        return redirect(adminRoute('user-groups', session()->get('admin/user-groups')))
            ->with('success', view('flash/success', ['message' => __('admin.usergroups.alert.remove.success', ['name' => $group->name])]));
    }

    private function getForm($model)
    {
        return form($model)
            ->add('name', 'translatable', ['label' => __('admin.usergroups.form.label.name'), 'constraints' => 'transrequired'])
            ->add('roles', 'tree', [
                'label' => __('admin.usergroups.form.label.roles'), 
                'tree_source' => (new \App\Models\UserRole())->getTree(),
                'tree_leaves' => true,
            ]);
    }

}
