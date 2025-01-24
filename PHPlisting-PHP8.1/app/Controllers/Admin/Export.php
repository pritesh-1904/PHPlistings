<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Export
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check(['admin_content', 'admin_export'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.export.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));
        
        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'desc';
        }

        $exports = \App\Models\Export::search(null, [], 'admin/' . $type->slug . '-export')
            ->where('type_id', $type->id)
            ->paginate();

        $table = dataTable($exports)
            ->addColumns([
                'id' => [__('admin.export.datatable.label.id')],
                'status' => [__('admin.export.datatable.label.status'), function ($export) {
                    return view('misc/status', [
                        'type' => 'export',
                        'status' => $export->status,
                    ]);
                }],
                'added_datetime' => [__('admin.export.datatable.label.added_datetime'), function($export) {
                    return locale()->formatDatetimeDiff($export->added_datetime);
                }],
            ])
            ->addActions([
                'download' => [__('admin.export.datatable.action.download'), function ($export) use ($type) {
                    if (in_array($export->status, ['done']) && file_exists($export->getPath())) {
                        return adminRoute($type->slug . '-export/download/' . $export->id);
                    }
                }],
                'delete' => [__('admin.export.datatable.action.delete'), function ($export) use ($type) {
                    if (in_array($export->status, ['queued', 'done'])) {
                        return adminRoute($type->slug . '-export/delete/' . $export->id);
                    }
                }],
            ])
            ->orderColumns([
                'added_datetime',
            ]);

        return response(layout()->content(
            view('admin/export/index', [
                'type' => $type,
                'exports' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check(['admin_content', 'admin_export'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.export.title.create', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $form = form()
            ->add('language_id', 'select', [
                'label' => __('admin.export.form.label.language'),
                'options' => \App\Models\Language::whereNotNull('active')->get()->pluck('name', 'id')->all(),
                'constraints' => 'required',
            ])
            ->add('categories', 'tree', [
                'label' => __('admin.export.form.label.categories'),
                'tree_source' => (new \App\Models\Category())->getExpandedTree($type->id),
                'constraints' => 'required',
            ])
            ->add('pricings', 'tree', [
                'label' => __('admin.export.form.label.pricings'),
                'tree_source' => (new \App\Models\Product())->getTreeWithHiddenPricing($type->id),
                'constraints' => 'required',
            ])
            ->add('fields', 'tree', [
                'label' => __('admin.export.form.label.fields'),
                'tree_source' => (new \App\Models\ListingField())->getTree($type->id, 1),
                'constraints' => 'required',
            ])
            ->add('submit', 'submit', ['label' => __('admin.export.form.label.submit')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                $export = new \App\Models\Export();
                $export->status = 'queued';
                $export->language_id = $input->get('language_id');
                $export->added_datetime = date("Y-m-d H:i:s");
                $type->exports()->save($export);

                $export->categories()->attach($input->categories);
                $export->pricings()->attach($input->pricings);
                $export->fields()->attach($input->fields);

                return redirect(adminRoute($type->slug . '-export', session()->get('admin/' . $type->slug . '-export')))
                    ->with('success', view('flash/success', ['message' => __('admin.export.alert.create.success', ['id' => $export->id])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }
        
        return response(layout()->content(
            view('admin/export/create', [
                'type' => $type,
                'form' => $form,
                'alert' => $alert ?? null,
            ])
        ));
    }

    public function actionDownload($params)
    {
        if (!auth()->check(['admin_content', 'admin_export'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $export = \App\Models\Export::where('id', $params['id'])->where('status', 'done')->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        return fileResponse($export->getPath())
            ->withHeaders([
                'Content-Disposition' => 'attachment; filename="export-' . $type->slug . '-' . $export->id . '.csv"',
            ]);
    }

    public function actionDelete($params)
    {
        if (!auth()->check(['admin_content', 'admin_export'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $export = \App\Models\Export::where('id', $params['id'])
            ->where('type_id', $type->id)
            ->where('status', '!=', 'running')
            ->first();

        if (null === $export) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $export->delete();

        return redirect(adminRoute($type->slug . '-export', session()->get('admin/' . $type->slug . '-export')))
            ->with('success', view('flash/success', ['message' => __('admin.export.alert.remove.success', ['id' => $export->id])]));
    }

}
