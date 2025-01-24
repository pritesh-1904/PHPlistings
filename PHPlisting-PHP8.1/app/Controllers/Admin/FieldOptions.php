<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class FieldOptions
    extends \App\Controllers\Admin\BaseController
{

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

        layout()->setTitle(__('admin.fieldoptions.' . $group->slug . '.title.index'));

        $options = $field
            ->options()
            ->orderBy('weight')
            ->paginate();

        $table = dataTable($options)
            ->addColumns([
                'name' => [__('admin.fieldoptions.datatable.label.name')],
                'value' => [__('admin.fieldoptions.datatable.label.value')],
            ])
            ->addActions([
                'edit' => [__('admin.fieldoptions.datatable.action.edit'), function ($option) use ($group, $field) {
                    return adminRoute('field-options/' . $group->slug . '/update/' . $option->id, ['field_id' => $field->id]);
                }],
                'delete' => [__('admin.fieldoptions.datatable.action.delete'), function ($option) use ($group, $field) {
                    if (null === $option->customizable) {
                        return null;
                    }

                    return adminRoute('field-options/' . $group->slug . '/delete/' . $option->id, ['field_id' => $field->id]);
                }],
            ])
            ->setSortable('field-options');

        return response(layout()->content(
            view('admin/field-options/index', [
                'group' => $group,
                'field' => $field,
                'options' => $table,
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

        layout()->setTitle(__('admin.fieldoptions.' . $group->slug . '.title.create'));

        $option = new \App\Models\FieldOption();

        $form = form($option)
            ->add('name', 'text', ['label' => __('admin.fieldoptions.form.label.name'), 'constraints' => 'required|alphanumeric'])
            ->add('value', 'translatable', ['label' => __('admin.fieldoptions.form.label.value'), 'constraints' => 'transrequired'])
            ->add('submit', 'submit', ['label' => __('admin.fieldoptions.form.label.submit')])
            ->handleRequest();
        
        if ($form->isSubmitted()) {           
            if ($form->isValid()) {
                $option->customizable = 1;

                $field->options()->save($option);

                return redirect(adminRoute('field-options/' . $group->slug, ['field_id' => $field->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.fieldoptions.alert.create.success', ['name' => $option->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/field-options/create', [
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

        if (null === $option = $field->options()->where('id', $params['id'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.fieldoptions.' . $group->slug . '.title.update'));

        $form = form($option)
            ->add('name', 'text', ['label' => __('admin.fieldoptions.form.label.name'), 'constraints' => 'required|alphanumeric'])
            ->add('value', 'translatable', ['label' => __('admin.fieldoptions.form.label.value'), 'constraints' => 'transrequired'])
            ->add('submit', 'submit', ['label' => __('admin.fieldoptions.form.label.update')]);

        if (null === $option->customizable) {
            $form->remove('name');
        }

        $form->handleRequest();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $field->options()->save($option);

                return redirect(adminRoute('field-options/' . $group->slug, ['field_id' => $field->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.fieldoptions.alert.update.success', ['name' => $option->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/field-options/update', [
                'group' => $group,
                'field' => $field,
                'option' => $option,
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

        if (null === $option = $field->options()->where('id', $params['id'])->whereNotNull('customizable')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }
        
        $option->delete();

        return redirect(adminRoute('field-options/' . $group->slug, ['field_id' => $field->id]))
            ->with('success', view('flash/success', ['message' => __('admin.fieldoptions.alert.remove.success', ['name' => $option->name])]));
    }

}
