<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class UploadTypes
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.uploadtypes.title.index'));

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'asc';
        }

        $uploadtypes = \App\Models\UploadType::search(null, [], 'admin/upload-types')
            ->paginate();

        $table = dataTable($uploadtypes)
            ->addColumns([
//                'id' => [__('admin.uploadtypes.datatable.label.id')],
                'name' => [__('admin.uploadtypes.datatable.label.name')],
            ])
            ->addActions([
                'edit' => [__('admin.uploadtypes.datatable.action.edit'), function ($type) {
                    return adminRoute('upload-types/update/' . $type->id);
                }],
                'delete' => [__('admin.uploadtypes.datatable.action.delete'), function ($type) {
                    if (null !== $type->customizable) {
                        return adminRoute('upload-types/delete/' . $type->id);
                    }
                }],
            ])
            ->orderColumns([
//                'id',
                'name',
            ]);

        return response(layout()->content(
            view('admin/upload-types/index', [
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

        layout()->setTitle(__('admin.uploadtypes.title.create'));

        $type = new \App\Models\UploadType();
        
        $form = $this->getForm($type)
            ->add('submit', 'submit', ['label' => __('admin.uploadtypes.form.label.submit')])
            ->handleRequest();
        
        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $type->customizable = 1;
                $type->public = 1;
                $type->save();

                $type->fileTypes()->attach($form->getValues()->filetypes);

                return redirect(adminRoute('upload-types', session()->get('admin/upload-types')))
                    ->with('success', view('flash/success', ['message' => __('admin.uploadtypes.alert.create.success', ['name' => $type->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/upload-types/create', [
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

        if (null === $type = \App\Models\UploadType::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.uploadtypes.title.update'));

        $form = $this->getForm($type)
            ->add('submit', 'submit', ['label' => __('admin.uploadtypes.form.label.update')])
            ->setValue('filetypes', $type->filetypes->pluck('id')->all())
            ->handleRequest();

        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $type->save();

                $type->fileTypes()->sync($form->getValues()->filetypes);

                return redirect(adminRoute('upload-types', session()->get('admin/upload-types')))
                    ->with('success', view('flash/success', ['message' => __('admin.uploadtypes.alert.update.success', ['name' => $type->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/upload-types/update', [
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

        if (null === $type = \App\Models\UploadType::where('id', $params['id'])->whereNotNull('customizable')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ($type->fields()->count() > 0 || $type->listingFields()->count() > 0) {
            return redirect(adminRoute('upload-types', session()->get('admin/upload-types')))
                ->with('error', view('flash/error', ['message' => __('admin.uploadtypes.alert.remove.failed', ['name' => $type->name])]));
        }

        $type->delete();

        return redirect(adminRoute('upload-types', session()->get('admin/upload-types')))
            ->with('success', view('flash/success', ['message' => __('admin.uploadtypes.alert.remove.success', ['name' => $type->name])]));
    }

    private function getForm($model)
    {
        $form = form($model)
            ->add('name', 'translatable', ['label' => __('admin.uploadtypes.form.label.name'), 'constraints' => 'transrequired'])
            ->add('max_files', 'number', ['label' => __('admin.uploadtypes.form.label.max_files'), 'constraints' => 'required|min:1'])
            ->add('max_size', 'number', ['label' => __('admin.uploadtypes.form.label.max_size'), 'constraints' => 'required|min:0.001']);

        foreach (['small', 'medium', 'large'] as $size) {
            $form
                ->add($size, 'separator')
                ->add($size . '_image_resize_type', 'select', ['label' => __('admin.uploadtypes.form.label.' . $size . '_image_resize_type'), 'options' => ['1' => __('admin.uploadtypes.form.option.manual'), '2' => __('admin.uploadtypes.form.option.crop'), '3' => __('admin.uploadtypes.form.option.fit')]])
                ->add($size . '_image_width', 'number', ['label' => __('admin.uploadtypes.form.label.' . $size . '_image_width'), 'constraints' => 'required|min:1'])
                ->add($size . '_image_height', 'number', ['label' => __('admin.uploadtypes.form.label.' . $size . '_image_height'), 'constraints' => 'required|min:1'])
                ->add($size . '_image_quality', 'number', ['label' => __('admin.uploadtypes.form.label.' . $size . '_image_quality'), 'value' => '95', 'constraints' => 'required|percent']);
        }

        $form
            ->add('watermark', 'separator')
            ->add('watermark_file_path', 'text', ['label' => __('admin.uploadtypes.form.label.watermark_file_path')])
            ->add('watermark_position_vertical', 'select', ['label' => __('admin.uploadtypes.form.label.watermark_position_vertical'), 'options' => ['top' => __('admin.uploadtypes.form.option.top'), 'center' => __('admin.uploadtypes.form.option.middle'), 'bottom' => __('admin.uploadtypes.form.option.bottom')]])
            ->add('watermark_position_horizontal', 'select', ['label' => __('admin.uploadtypes.form.label.watermark_position_horizontal'), 'options' => ['left' => __('admin.uploadtypes.form.option.left'), 'center' => __('admin.uploadtypes.form.option.center'), 'right' => __('admin.uploadtypes.form.option.right')]])
            ->add('watermark_transparency', 'number', ['label' => __('admin.uploadtypes.form.label.watermark_transparency'), 'value' => '50', 'constraints' => 'required|percent'])
            ->add('cropbox', 'separator')
            ->add('cropbox_width', 'number', ['label' => __('admin.uploadtypes.form.label.cropbox_width'), 'constraints' => 'required|min:1'])
            ->add('cropbox_height', 'number', ['label' => __('admin.uploadtypes.form.label.cropbox_height'), 'constraints' => 'required|min:1'])
            ->add('types', 'separator')
            ->add('filetypes', 'tree', [
                'label' => __('admin.uploadtypes.form.label.filetypes'), 
                'tree_source' => (new \App\Models\FileType())->getTree(),
                'constraints' => 'required|minlength:1',
            ]);

       return $form;
    }

}
