<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Pricings
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (false === auth()->check(['admin_content', 'admin_products'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === request()->get->get('product_id') || null === $product = $type->products()->where('id', request()->get->product_id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.pricings.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural, 'product' => $product->name]));

        $pricings = $product->pricings()
            ->orderBy('weight')
            ->paginate();

        $table = dataTable($pricings)
            ->addColumns([
                'id' => [__('admin.pricings.datatable.label.id')],
                'name' => [__('admin.pricings.datatable.label.name'), function ($pricing) {
                    return $pricing->getName();
                }],
                'hidden' => [__('admin.pricings.datatable.label.hidden'), function ($pricing) {
                    return view('misc/ajax-switch', [
                        'table' => 'pricings',
                        'column' => 'hidden',
                        'id' => $pricing->id,
                        'value' => $pricing->hidden,
                    ]);
                }],
            ])
            ->addActions([
                'edit' => [__('admin.pricings.datatable.action.edit'), function ($pricing) use ($type, $product) {
                    return adminRoute('pricings/' . $type->slug . '/update/' . $pricing->id, ['product_id' => $product->id]);
                }],
                'delete' => [__('admin.pricings.datatable.action.delete'), function ($pricing) use ($type, $product) {
                    return adminRoute('pricings/' . $type->slug . '/delete/' . $pricing->id, ['product_id' => $product->id]);
                }],
            ])
            ->setSortable('pricings');

        return response(layout()->content(
            view('admin/pricings/index', [
                'type' => $type,
                'product' => $product,
                'pricings' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (false === auth()->check(['admin_content', 'admin_products'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === request()->get->get('product_id') || null === $product = $type->products()->where('id', request()->get->product_id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.pricings.title.create', ['singular' => $type->name_singular, 'plural' => $type->name_plural, 'product' => $product->name]));

        $pricing = new \App\Models\Pricing();

        $form = $this->getForm($pricing, $type)
            ->add('submit', 'submit', ['label' => __('admin.pricings.form.label.submit')])
            ->handleRequest();
        
        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                if (null !== $input->get('upgrade_id')) {
                    $pricing->upgrade_id = $input->get('upgrade_id')[0];
                }

                $product->pricings()->save($pricing);

                $pricing->upgrades()->attach($input->upgrades);
                $pricing->required()->attach($input->required);
                $pricing->gateways()->attach($input->gateways);

                return redirect(adminRoute('pricings/' . $type->slug, ['product_id' => $product->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.pricings.alert.create.success', ['name' => $pricing->getName()])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/pricings/create', [
                'type' => $type,
                'product' => $product,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (false === auth()->check(['admin_content', 'admin_products'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === request()->get->get('product_id') || null === $product = $type->products()->where('id', request()->get->product_id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $pricing = $product->pricings()->where('id', $params['id'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.pricings.title.update', ['singular' => $type->name_singular, 'plural' => $type->name_plural, 'product' => $product->name]));

        $form = $this->getForm($pricing, $type)
            ->add('sync', 'toggle', ['label' => __('admin.pricings.form.label.sync'), 'value' => 1])
            ->add('submit', 'submit', ['label' => __('admin.pricings.form.label.update')])
            ->setValue('upgrades', $pricing->upgrades->pluck('id')->all())
            ->setValue('required', $pricing->required->pluck('id')->all())
            ->setValue('gateways', $pricing->gateways->pluck('id')->all())
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                if (null !== $input->get('upgrade_id')) {
                    $pricing->upgrade_id = $input->get('upgrade_id')[0];
                }

                $pricing->save();

                $pricing->upgrades()->sync($input->upgrades);
                $pricing->required()->sync($input->required);
                $pricing->gateways()->sync($input->gateways);

                if (null !== $input->get('sync')) {
                    \App\Models\Order::where('pricing_id', $pricing->id)
                        ->update(['sync_pricing' => 1]);
                }

                return redirect(adminRoute('pricings/' . $type->slug, ['product_id' => $product->id]))
                    ->with('success', view('flash/success', ['message' => __('admin.pricings.alert.update.success', ['name' => $pricing->getName()])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/pricings/update', [
                'type' => $type,
                'product' => $product,
                'form' => $form,
                'alert' => $alert ?? null,
                'pricing' => $pricing,
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (false === auth()->check(['admin_content', 'admin_products'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === request()->get->get('product_id') || null === $product = $type->products()->where('id', request()->get->product_id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $pricing = $product->pricings()->where('id', $params['id'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ($pricing->orders()->count() > 0) {
            return redirect(adminRoute('pricings/' . $type->slug, ['product_id' => $product->id]))
                ->with('error', view('flash/error', ['message' => __('admin.pricings.alert.remove.failed', ['name' => $pricing->getName()])]));
        }

        $pricing->delete();

        return redirect(adminRoute('pricings/' . $type->slug, ['product_id' => $product->id]))
            ->with('success', view('flash/success', ['message' => __('admin.pricings.alert.remove.success', ['name' => $pricing->getName()])]));
    }


    private function getForm($model, $type)
    {
        return form($model)
            ->add('autoapprovable', 'toggle', ['label' => __('admin.pricings.form.label.approvable')])
            ->add('claimable', 'toggle', ['label' => __('admin.pricings.form.label.claimable')])
            ->add('cancellable', 'toggle', ['label' => __('admin.pricings.form.label.cancellable')])
            ->add('hidden', 'toggle', ['label' => __('admin.pricings.form.label.hidden')])

            ->add('period', 'select', ['label' => __('admin.pricings.form.label.period'), 'options' => ['Y' => __('admin.pricings.form.label.period_year'), 'M' => __('admin.pricings.form.label.period_month'), 'D' => __('admin.pricings.form.label.period_day')]])
            ->add('period_count', 'number', ['label' => __('admin.pricings.form.label.period_count'), 'value' => '1', 'constraints' => 'required|min:1'])
            ->add('price', 'price', ['label' => __('admin.pricings.form.label.price'), 'value' => '0.00', 'constraints' => 'required|min:0|max:9999999999'])
            ->add('upgrades', 'tree', [
                'label' => __('admin.pricings.form.label.upgrades'), 
                'tree_source' => (new \App\Models\Product())->getTreeWithHiddenPricing($type->id),
            ])
            ->add('required', 'tree', [
                'label' => __('admin.pricings.form.label.required'), 
                'tree_source' => (new \App\Models\Product())->getTreeWithHiddenPricing(),
            ])
            ->add('user_limit', 'number', ['label' => __('admin.pricings.form.label.user_limit'), 'value' => '0', 'constraints' => 'required|min:0'])
            ->add('peruser_limit', 'number', ['label' => __('admin.pricings.form.label.peruser_limit'), 'value' => '0', 'constraints' => 'required|min:0'])
            ->add('gateways', 'tree', [
                'label' => __('admin.pricings.form.label.gateways'), 
                'tree_source' => (new \App\Models\Gateway())->getTree(),
                'constraints' => 'required',
            ]);
    }

}
