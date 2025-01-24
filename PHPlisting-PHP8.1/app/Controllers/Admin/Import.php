<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Import
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check(['admin_content', 'admin_import'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.import.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));
        
        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'desc';
        }

        $imports = \App\Models\Import::search(null, [], 'admin/' . $type->slug . '-import')
            ->where('type_id', $type->id)
            ->paginate();

        $table = dataTable($imports)
            ->addColumns([
                'id' => [__('admin.import.datatable.label.id')],
                'status' => [__('admin.import.datatable.label.status'), function ($import) {
                    return view('misc/status', [
                        'type' => 'import',
                        'status' => $import->status,
                    ]);
                }],
                'added_datetime' => [__('admin.import.datatable.label.added_datetime'), function ($import) {
                    return locale()->formatDatetimeDiff($import->added_datetime);
                }],
            ])
            ->addActions([
                'download' => [__('admin.import.datatable.action.download'), function ($import) use ($type) {
                    if (in_array($import->status, ['done']) && file_exists($import->getLogPath())) {
                        return adminRoute($type->slug . '-import/download/' . $import->id);
                    }
                }],
                'delete' => [__('admin.import.datatable.action.delete'), function ($import) use ($type) {
                    if (false !== in_array($import->status, ['queued', 'done'])) {
                        return adminRoute($type->slug . '-import/delete/' . $import->id);
                    }
                }],
            ])
            ->orderColumns([
                'added_datetime',
            ]);

        return response(layout()->content(
            view('admin/import/index', [
                'type' => $type,
                'imports' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check(['admin_content', 'admin_import'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.import.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $form = form()
            ->setEnctype('multipart/form-data')
            ->add('csv', 'file', [
                'label' => __('admin.import.form.label.file'),
            ])
            ->add('language_id', 'select', [
                'label' => __('admin.import.form.label.language'),
                'options' => \App\Models\Language::whereNotNull('active')->get()->pluck('name', 'id')->all(),
                'constraints' => 'required',
            ])
            ->add('pricing_id', 'tree', [
                'label' => __('admin.import.form.label.pricing'),
                'tree_source' => (new \App\Models\Product())->getTreeWithHiddenPricing($type->id),
                'constraints' => 'required|maxlength:1',
            ])
            ->add('user_id', 'user', [
                'label' => __('admin.import.form.label.user'),
                'constraints' => 'required',
            ])
            ->add('active', 'toggle', [
                'label' => __('admin.import.form.label.active'),
                'value' => 1,
            ])
            ->add('claimed', 'toggle', [
                'label' => __('admin.import.form.label.claimed'),
                'value' => 1,
            ])
            ->add('notification', 'toggle', [
                'label' => __('admin.import.form.label.notification'),
            ])
            ->add('submit', 'submit', ['label' => __('admin.import.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            $uploadedFile = request()->files->get('csv');

            if (null === $uploadedFile || false === $uploadedFile->isFile() || false === $uploadedFile->isReadable()) {
                $form->setValidationError('csv', __('form.validation.required'));
            }

            if ($form->isValid()) {
                $file = fopen($uploadedFile->getPathname(), 'r');

                if (false === $file) {
                    $form->setValidationError('csv', __('admin.import.form.error.io'));
                }

                $header = fgetcsv($file);

                if (null === $header || false === $header || false === is_array($header) || null === $header[0]) {
                    $form->setValidationError('csv', __('admin.import.form.error.parse'));
                }

                if (substr($header[0], 0, 3) === pack('CCC', 0xEF, 0xBB, 0xBF)) {
                    $header[0] = substr($header[0], 3);
                }

                $fields = $type->fields()
                    ->where('listingfieldgroup_id', 1)
                    ->whereNull('customizable')
                    ->get()

                    ->pluck('name')
                    ->push('category_id')
                    ->all();

                if (count($header) !== count(array_unique($header))) {
                    $form->setValidationError('csv', __('admin.import.form.error.duplicate'));
                }

                foreach ($fields as $key => $field) {
                    if (false !== in_array($field, $header)) {
                        unset($fields[$key]);
                    }
                }

                if (count($fields) > 0) {
                    $form->setValidationError('csv', __('admin.import.form.error.fields', ['fields' => implode(', ', $fields)], count($fields)));
                }

                $file = new \SplFileObject($uploadedFile->getPathname(), 'r');

                $file->setFlags(
                    \SplFileObject::READ_CSV |
                    \SplFileObject::READ_AHEAD |
                    \SplFileObject::SKIP_EMPTY |
                    \SplFileObject::DROP_NEW_LINE
                );

                $file->seek(PHP_INT_MAX);

                if ($file->key() + 1 > 10000) {
                    $form->setValidationError('csv', __('admin.import.form.error.limit'));
                }
            }

            if ($form->isValid()) {
                $import = new \App\Models\Import();
                $import->language_id = $input->get('language_id');
                $import->pricing_id = $input->get('pricing_id')[0];
                $import->user_id = $input->get('user_id');
                $import->status = 'queued';
                $import->active = $input->get('active');
                $import->claimed = $input->get('claimed');
                $import->notification = $input->get('notification');
                $import->added_datetime = date("Y-m-d H:i:s");
                $type->imports()->save($import);

                $uploadedFile->store($import->getPath());

                return redirect(adminRoute($type->slug . '-import', session()->get('admin/' . $type->slug . '-import')))
                    ->with('success', view('flash/success', ['message' => __('admin.import.alert.create.success', ['id' => $import->id])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }
        
        return response(layout()->content(
            view('admin/import/create', [
                'type' => $type,
                'form' => $form,
                'alert' => $alert ?? null,
            ])
        ));
    }

    public function actionDownload($params)
    {
        if (!auth()->check(['admin_content', 'admin_import'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $import = \App\Models\Import::where('id', $params['id'])->where('status', 'done')->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        return fileResponse($import->getLogPath())
            ->withHeaders([
                'Content-Disposition' => 'attachment; filename="import-' . $type->slug . '-' . $import->id . '.log"',
            ]);
    }

    public function actionDelete($params)
    {
        if (!auth()->check(['admin_content', 'admin_import'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $import = \App\Models\Import::where('id', $params['id'])
            ->where('type_id', $type->id)
            ->where('status', '!=', 'running')
            ->first();

        if (null === $import) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $import->delete();

        return redirect(adminRoute($type->slug . '-import', session()->get('admin/' . $type->slug . '-import')))
            ->with('success', view('flash/success', ['message' => __('admin.import.alert.remove.success', ['id' => $import->id])]));
    }

}
