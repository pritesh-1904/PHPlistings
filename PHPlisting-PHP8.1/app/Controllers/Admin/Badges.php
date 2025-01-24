<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Badges
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (false === auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.badges.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $badges = \App\Models\Badge::search(null, [], 'admin/' . $type->slug . '-badges')
            ->where('type_id', $type->id)
            ->orderBy('weight')
            ->paginate();

        $table = dataTable($badges)
            ->addColumns([
                'name' => [__('admin.badges.datatable.label.name')],
                'active' => [__('admin.badges.datatable.label.published'), function ($badge) {
                    return view('misc/ajax-switch', [
                        'table' => 'badges',
                        'column' => 'active',
                        'id' => $badge->id,
                        'value' => $badge->active
                    ]);
                }],
            ])
            ->addActions([
                'edit' => [__('admin.badges.datatable.action.edit'), function ($badge) use ($type) {
                    return adminRoute($type->slug . '-badges/update/' . $badge->id);
                }],
                'delete' => [__('admin.badges.datatable.action.delete'), function ($badge) use ($type) {
                    return adminRoute($type->slug . '-badges/delete/' . $badge->id);
                }],
            ])
            ->setSortable('badges');

        return response(layout()->content(
            view('admin/badges/index', [
                'type' => $type,
                'badges' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (false === auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.badges.title.create', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $badge = new \App\Models\Badge();

        $form = $this->getForm($badge)
            ->add('submit', 'submit', ['label' => __('admin.badges.form.label.submit', ['singular' => $type->name_singular, 'plural' => $type->name_plural])])
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                $badge->type_id = $type->id;
                $badge->save();

                return redirect(adminRoute($type->slug . '-badges', session()->get('admin/' . $type->slug . '-badges')))
                    ->with('success', view('flash/success', ['message' => __('admin.badges.alert.create.success', ['name' => $badge->name, 'singular' => $type->name_singular, 'plural' => $type->name_plural])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/badges/create', [
                'type' => $type,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (false === auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $badge = \App\Models\Badge::where('id', $params['id'])->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.badges.title.update', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $form = $this->getForm($badge);

        $form
            ->add('submit', 'submit', ['label' => __('admin.badges.form.label.update', ['singular' => $type->name_singular, 'plural' => $type->name_plural])])
            ->handleRequest();

        if ($form->isSubmitted()) {           
            $input = $form->getValues();

            if ($form->isValid()) {
                $badge->save();

                return redirect(adminRoute($type->slug . '-badges', session()->get('admin/' . $type->slug . '-badges')))
                    ->with('success', view('flash/success', ['message' => __('admin.badges.alert.update.success', ['name' => $badge->name, 'singular' => $type->name_singular, 'plural' => $type->name_plural])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/badges/update', [
                'type' => $type,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (false === auth()->check(['admin_content'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $badge = \App\Models\Badge::where('id', $params['id'])->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $badge->delete();

        return redirect(adminRoute($type->slug . '-badges', session()->get('admin/' . $type->slug . '-badges')))
            ->with('success', view('flash/success', ['message' => __('admin.badges.alert.remove.success', ['name' => $badge->name, 'singular' => $type->name_singular, 'plural' => $type->name_plural])]));
    }

    private function getForm($model)
    {
        return form($model)
            ->add('active', 'toggle', ['label' => __('admin.badges.form.label.published'), 'value' => 1])
            ->add('name', 'translatable', ['label' => __('admin.badges.form.label.name'), 'constraints' => 'transrequired|transmaxlength:120'])
            ->add('image_id', 'dropzone', ['label' => __('admin.badges.form.label.image'), 'constraints' => 'filerequired', 'upload_id' => 40]);
    }

}
