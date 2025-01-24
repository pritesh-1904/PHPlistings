<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Gateways
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.gateways.title.index'));

        $gateways = \App\Models\Gateway::search(null, [], 'admin/payment-gateways')
            ->orderBy('weight')
            ->paginate();

        $table = dataTable($gateways)
            ->addColumns([
                'name' => [__('admin.gateways.datatable.label.name')],
                'active' => [__('admin.gateways.datatable.label.published'), function ($gateway) {
                    return view('misc/ajax-switch', [
                        'table' => 'gateways',
                        'column' => 'active',
                        'id' => $gateway->id,
                        'value' => $gateway->active
                    ]);
                }],
            ])
            ->addActions([
                'edit' => [__('admin.gateways.datatable.action.edit'), function ($gateway) {
                    return adminRoute('payment-gateways/update/' . $gateway->id);
                }],
            ])
            ->setSortable('gateways');

        return response(layout()->content(
            view('admin/gateways/index', [
                'gateways' => $table,
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $gateway = \App\Models\Gateway::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.gateways.title.update'));

        $form = form($gateway)
            ->add('active', 'toggle', ['label' => __('admin.gateways.form.label.published'), 'value' => 1])
            ->add('name', 'translatable', ['label' => __('admin.gateways.form.label.name'), 'constraints' => 'transrequired'])
            ->add('description', 'translatable', ['label' => __('admin.gateways.form.label.description')])
            ->add('separator', 'separator');

        $gateway
            ->getGatewayObject()
            ->getConfigurationForm($form)
            ->setValues($gateway->getSettings());

        $form
            ->add('submit', 'submit', ['label' => __('admin.gateways.form.label.update', ['name' => $gateway->name])])
            ->handleRequest();

        if ($form->isSubmitted()) {           
            $input = $form->getValues();

            if ($form->isValid()) {                
                $settings = [];

                foreach ($gateway->getGatewayObject()->getConfigurationForm(form())->getFields() as $field) {
                    $settings[$field->name] = $input->get($field->name);
                }

                $gateway->setSettings($settings);
                $gateway->save();

                return redirect(adminRoute('payment-gateways', session()->get('admin/payment-gateways')))
                    ->with('success', view('flash/success', ['message' => __('admin.gateways.alert.update.success', ['name' => $gateway->name])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/gateways/update', [
                'form' => $form,
                'alert' => $alert ?? null,
                'gateway' => $gateway,
            ])
        ));
    }

}
