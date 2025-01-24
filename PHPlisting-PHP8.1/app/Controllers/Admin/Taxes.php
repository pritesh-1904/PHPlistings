<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Taxes
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.taxes.title.index'));

        $taxes = \App\Models\Tax::search(null, [], 'admin/tax-rates')
            ->orderBy('weight')
            ->paginate();

        $table = dataTable($taxes)
            ->addColumns([
                'id' => [__('admin.taxes.datatable.label.id')],
                'name' => [__('admin.taxes.datatable.label.name')],
                'value' => [__('admin.taxes.datatable.label.value'), function ($tax) {
                    return locale()->formatNumber($tax->value) . '%';
                }],
                'compound' => [__('admin.taxes.datatable.label.compound'), function ($tax) {
                    return view('misc/ajax-switch', [
                        'table' => 'taxes',
                        'column' => 'compound',
                        'id' => $tax->id,
                        'value' => $tax->compound,
                    ]);
                }],
            ])
            ->addActions([
                'edit' => [__('admin.taxes.datatable.action.edit'), function ($tax) {
                    return adminRoute('tax-rates/update/' . $tax->id);
                }],
                'delete' => [__('admin.taxes.datatable.action.delete'), function ($tax) {
                    return adminRoute('tax-rates/delete/' . $tax->id);
                }],
            ])
            ->setSortable('taxes');

        return response(layout()->content(
            view('admin/tax-rates/index', [
                'taxes' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.taxes.title.create'));

        $tax = new \App\Models\Tax();

        $form = $this->getForm($tax)
            ->add('submit', 'submit', ['label' => __('admin.taxes.form.label.submit')])
            ->handleRequest();
        
        if ($form->isSubmitted()) {           
            if ($form->isValid()) {
                if ('' == $tax->get('location_id', '')) {
                    $tax->put('location_id', null);
                }
                
                $tax->save();

                return redirect(adminRoute('tax-rates', session()->get('admin/tax-rates')))
                    ->with('success', view('flash/success', ['message' => __('admin.taxes.alert.create.success', ['name' => $tax->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/tax-rates/create', [
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

        if (null === $tax = \App\Models\Tax::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.taxes.title.update'));

        $form = $this->getForm($tax)
            ->add('submit', 'submit', ['label' => __('admin.taxes.form.label.update')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ('' == $tax->get('location_id', '')) {
                    $tax->put('location_id', null);
                }

                $tax->save();

                return redirect(adminRoute('tax-rates', session()->get('admin/tax-rates')))
                    ->with('success', view('flash/success', ['message' => __('admin.taxes.alert.update.success', ['name' => $tax->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/tax-rates/update', [
                'tax' => $tax,
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

        if (null === $tax = \App\Models\Tax::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $tax->delete();

        return redirect(adminRoute('tax-rates', session()->get('admin/tax-rates')))
            ->with('success', view('flash/success', ['message' => __('admin.taxes.alert.remove.success', ['name' => $tax->name])]));
    }

    private function getForm($model)
    {
        return form($model)
            ->add('name', 'translatable', ['label' => __('admin.taxes.form.label.name'), 'constraints' => 'transrequired'])
            ->add('value', 'number', ['label' => __('admin.taxes.form.label.value'), 'constraints' => 'required|percent'])
            ->add('compound', 'toggle', ['label' => __('admin.taxes.form.label.compound')])
            ->add('location_id', 'cascading', [
                'label' => __('admin.taxes.form.label.location'),
                'cascading_source' => 'location',
            ]);
    }

}
