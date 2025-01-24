<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Menu
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\WidgetMenuGroup::find($params['group'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (!isset(request()->get->parent_id)) {
            request()->get->parent_id = 0;
        }

        layout()->setTitle(__('admin.menu.title.index', ['group' => $group->name]));

        $menuItems = \App\Models\WidgetMenuItem::search(null, [], 'admin/menu/' . $group->id)
            ->where('widgetmenugroup_id', $group->id)
            ->orderBy('weight')
            ->paginate(25);

        $table = dataTable($menuItems)
            ->addColumns([
                'id' => [__('admin.menu.datatable.label.id')],
                'name' => [__('admin.menu.datatable.label.name')],
                'active' => [__('admin.menu.datatable.label.published'), function ($item) {
                    return view('misc/ajax-switch', [
                        'table' => 'widgetmenuitems',
                        'column' => 'active',
                        'id' => $item->id,
                        'value' => $item->active
                    ]);
                }],
                'public' => [__('admin.menu.datatable.label.public'), function ($item) {
                    return view('misc/ajax-switch', [
                        'table' => 'widgetmenuitems',
                        'column' => 'public',
                        'id' => $item->id,
                        'value' => $item->public
                    ]);
                }],
                'highlighted' => [__('admin.menu.datatable.label.highlighted'), function ($item) {
                    return view('misc/ajax-switch', [
                        'table' => 'widgetmenuitems',
                        'column' => 'highlighted',
                        'id' => $item->id,
                        'value' => $item->highlighted
                    ]);
                }],
            ])
            ->addActions([
                'edit' => [__('admin.menu.datatable.action.edit'), function ($item) use ($group) {
                    return adminRoute('menu/' . $group->id. '/update/' . $item->id);
                }],
                'children' => [__('admin.menu.datatable.action.children'), function ($item) use ($group) {
                    if (count($item->children) > 0) {
                        return adminRoute('menu/' . $group->id,  ['parent_id' => $item->id]);
                    }
                }],
                'delete' => [__('admin.menu.datatable.action.delete'), function ($item) use ($group) {
                    return adminRoute('menu/' . $group->id . '/delete/' . $item->id);
                }],
            ])
            ->setSortable('widget-menu');

        return response(layout()->content(
            view('admin/menu/index', [
                'group' => $group,
                'menuItems' => $table,
                'parent' => \App\Models\WidgetMenuItem::find(request()->get->parent_id),
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_appearance')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $group = \App\Models\WidgetMenuGroup::find($params['group'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.menu.title.create', ['group' => $group->name]));

        $item = new \App\Models\WidgetMenuItem();

        $form = $this->getForm($item, $group)
            ->add('submit', 'submit', ['label' => __('admin.menu.form.label.submit')])
            ->handleRequest();
        
        if ($form->isSubmitted()) {           
            $input = $form->getValues();

            if ($form->isValid()) {
                $group->menuItems()->save($item);

                return redirect(adminRoute('menu/' . $group->id, session()->get('admin/menu/' . $group->id)))
                    ->with('success', view('flash/success', ['message' => __('admin.menu.alert.create.success', ['name' => $item->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/menu/create', [
                'group' => $group,
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

        if (null === $group = \App\Models\WidgetMenuGroup::find($params['group'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $item = $group->menuItems()->where('id', $params['id'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.menu.title.update', ['group' => $group->name]));

        $form = $this->getForm($item, $group)
            ->add('submit', 'submit', ['label' => __('admin.menu.form.label.update')])
            ->handleRequest();

        if ($form->isSubmitted()) {           
            $input = $form->getValues();

            if ($form->isValid()) {
                $item->save();

                return redirect(adminRoute('menu/' . $group->id, session()->get('admin/menu/' . $group->id)))
                    ->with('success', view('flash/success', ['message' => __('admin.menu.alert.update.success', ['name' => $item->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/menu/update', [
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

        if (null === $group = \App\Models\WidgetMenuGroup::find($params['group'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $item = $group->menuItems()->where('id', $params['id'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $item->delete();

        return redirect(adminRoute('menu/' . $group->id, session()->get('admin/menu/' . $group->id)))
            ->with('success', view('flash/success', ['message' => __('admin.menu.alert.remove.success', ['name' => $item->name])]));
    }

    private function getForm($model, $group)
    {
        return form($model)
            ->add('active', 'toggle', ['label' => __('admin.menu.form.label.published'), 'value' => '1'])
            ->add('public', 'toggle', ['label' => __('admin.menu.form.label.public'), 'value' => '1'])
            ->add('highlighted', 'toggle', ['label' => __('admin.menu.form.label.highlighted')])
            ->add('_parent_id', 'select', ['label' => __('admin.menu.form.label.parent'), 'options' => ['0' => __('admin.menu.form.option.no_parent')] + $group->getParentsDropdownTree(), 'constraints' => 'required'])
            ->add('name', 'translatable', ['label' => __('admin.menu.form.label.name'), 'constraints' => 'transrequired'])
            ->add('route', 'text', ['label' => __('admin.menu.form.label.route'), 'constraints' => 'alphanumericdashslash'])
            ->add('link', 'url', ['label' => __('admin.menu.form.label.link')])
            ->add('target', 'select', ['label' => __('admin.menu.form.label.target'), 'value' => '_self', 'options' => ['_blank' => __('admin.menu.form.option.blank'), '_self' => __('admin.menu.form.option.self'), '_parent' => __('admin.menu.form.option.parent'), '_top' => __('admin.menu.form.option.top'), ]])
            ->add('nofollow', 'toggle', ['label' => __('admin.menu.form.label.no_follow')]);
    }

}
