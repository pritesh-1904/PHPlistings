<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Files
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_files')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.files.title.index'));

        if (null !== request()->post->get('action') && null !== request()->post->get('id')) {
            switch (request()->post->action) {
                case 'delete':
                    foreach ((array) request()->post->get('id') as $id) {
                        $file = \App\Models\File::find($id);
                        if (null !== $file) {
                            $file->delete();
                        }
                    }

                    $alert = view('flash/success', ['message' => __('admin.files.alert.remove.success')]);
                    break;
            }
        }

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'asc';
        }

        $files = \App\Models\File::search(null, [], 'admin/files')
            ->with('type')
            ->paginate();

        $table = dataTable($files)
            ->addColumns([
                'id' => [__('admin.files.datatable.label.id')],
                'name' => [__('admin.files.datatable.label.name'), function ($file) {
                    return $file->name . '.' . $file->extension;
                }],
                'type' => [__('admin.files.datatable.label.type'), function ($file) {
                    return $file->type->name;
                }],
                'mime' => [__('admin.files.datatable.label.mime')],
                'size' => [__('admin.files.datatable.label.size'), function ($file) {
                    return locale()->formatFilesize($file->size);
                }],
            ])
            ->orderColumns([
                'id',
                'name',
                'size',
            ])
            ->addActions([
                'download' => [__('admin.files.datatable.action.download'), function ($file) {
                    return $file->getUrl();
                }],
                'delete' => [__('admin.files.datatable.action.delete'), function ($file) {
                    return adminRoute('files/delete/' . $file->id);
                }],
            ])
            ->addBulkActions([
                'delete' => __('admin.files.datatable.bulkaction.delete'),
            ]);

        $form = form()
            ->setMethod('get')
            ->setTemplate('form/inline')
            ->add('uploadtype_id', 'select', [
                'options' => ['' => __('admin.files.form.label.filter_by_type')] + \App\Models\UploadType::orderBy('id')->get(['name', 'id'])->pluck('name', 'id')->all(),
                'weight' => 10,
            ])
            ->add('submit', 'submit', [
                'label' => __('admin.files.form.label.submit')
            ])
            ->forceRequest();

        return response(layout()->content(
            view('admin/files/index', [
                'files' => $table,
                'form' => $form,
                'alert' => $alert ?? null,
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (!auth()->check('admin_files')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $file = \App\Models\File::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $file->delete();

        return redirect(adminRoute('files', session()->get('admin/files')))
            ->with('success', view('flash/success', ['message' => __('admin.files.alert.remove.success')]));
    }

}
