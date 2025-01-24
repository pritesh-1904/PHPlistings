<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class FileTypes
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.filetypes.title.index'));

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'asc';
        }

        $filetypes = \App\Models\FileType::search(null, [], 'admin/file-types')
            ->paginate();

        $table = dataTable($filetypes)
            ->addColumns([
                'id' => [__('admin.filetypes.datatable.label.id')],
                'name' => [__('admin.filetypes.datatable.label.name')],
            ])
            ->addActions([
                'edit' => [__('admin.filetypes.datatable.action.edit'), function ($type) {
                    return adminRoute('file-types/update/' . $type->id);
                }],
                'delete' => [__('admin.filetypes.datatable.action.delete'), function ($type) {
                    return adminRoute('file-types/delete/' . $type->id);
                }],
            ])
            ->orderColumns([
                'id',
                'name',
            ]);

        return response(layout()->content(
            view('admin/file-types/index', [
                'types' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.filetypes.title.create'));

        $type = new \App\Models\FileType();
        
        $form = $this->getForm($type)
            ->add('submit', 'submit', ['label' => __('admin.filetypes.form.label.submit')])
            ->handleRequest();
        
        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $type->save();

                $type->uploadTypes()->attach($form->getValues()->uploadtypes);

                return redirect(adminRoute('file-types', session()->get('admin/file-types')))
                    ->with('success', view('flash/success', ['message' => __('admin.filetypes.alert.create.success', ['name' => $type->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/file-types/create', [
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\FileType::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.filetypes.title.update'));

        $form = $this->getForm($type)
            ->add('submit', 'submit', ['label' => __('admin.filetypes.form.label.update')])
            ->setValue('uploadtypes', $type->uploadtypes->pluck('id')->all())
            ->handleRequest();

        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $type->save();

                $type->uploadTypes()->sync($form->getValues()->uploadtypes);

                return redirect(adminRoute('file-types', session()->get('admin/file-types')))
                    ->with('success', view('flash/success', ['message' => __('admin.filetypes.alert.update.success', ['name' => $type->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/file-types/update', [
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\FileType::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $type->delete();

        return redirect(adminRoute('file-types', session()->get('admin/file-types')))
            ->with('success', view('flash/success', ['message' => __('admin.filetypes.alert.remove.success', ['name' => $type->name])]));
    }

    protected function getForm($model)
    {
        $form = form($model)
            ->add('name', 'translatable', ['label' => __('admin.filetypes.form.label.name'), 'constraints' => 'transrequired'])
            ->add('mime', 'text', ['label' => __('admin.filetypes.form.label.mime'), 'constraints' => 'required'])
            ->add('extension', 'text', ['label' => __('admin.filetypes.form.label.extension'), 'constraints' => 'required'])
            ->add('uploadtypes', 'tree', [
                'label' => __('admin.filetypes.form.label.uploadtypes'), 
                'tree_source' => (new \App\Models\UploadType())->getTree(),
                'constraints' => 'required|minlength:1',
            ]);

       return $form;
    }

}
