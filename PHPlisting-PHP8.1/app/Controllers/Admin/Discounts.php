<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Discounts
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.discounts.title.index'));

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'desc';
        }

        $discounts = \App\Models\Discount::search(null, [], 'admin/discounts')
            ->paginate();

        $table = dataTable($discounts)
            ->addColumns([
                'code' => [__('admin.discounts.datatable.label.code')],
                'active' => [__('admin.discounts.datatable.label.published'), function ($discount) {
                    return view('misc/ajax-switch', [
                        'table' => 'discounts',
                        'column' => 'active',
                        'id' => $discount->id,
                        'value' => $discount->active
                    ]);
                }],
                'end_date' => [__('admin.discounts.datatable.label.end_date'), function ($discount) {
                    return locale()->formatDate($discount->end_date);
                }],
            ])
            ->addActions([
                'edit' => [__('admin.discounts.datatable.action.edit'), function ($discount) {
                    return adminRoute('discounts/update/' . $discount->id);
                }],
                'delete' => [__('admin.discounts.datatable.action.delete'), function ($discount) {
                    return adminRoute('discounts/delete/' . $discount->id);
                }],
            ])
            ->orderColumns([
                'end_date',
            ]);

        return response(layout()->content(
            view('admin/discounts/index', [
                'discounts' => $table,
            ])
        ));
    }

    public function actionCreate($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.discounts.title.create'));

        $discount = new \App\Models\Discount();

        $form = $this->getForm($discount)
            ->add('submit', 'submit', ['label' => __('admin.discounts.form.label.submit')])
            ->handleRequest();
        
        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if ($form->isValid()) {
                if (strtotime($input->get('start_date')) > strtotime($input->get('end_date'))) {
                    $form->setValidationError('end_date', __('admin.discounts.alert.start_after_end'));
                }

                if ('percentage' == $input->get('type') && ($input->get('amount') < 0 || $input->get('amount') > 100)) {
                    $form->setValidationError('amount', __('admin.discounts.alert.invalid_percentage'));
                }
            }

            if ($form->isValid()) {
                $discount->save();

                $discount->pricings()->attach($input->pricings);
                $discount->required()->attach($input->required);

                return redirect(adminRoute('discounts', session()->get('admin/discounts')))
                    ->with('success', view('flash/success', ['message' => __('admin.discounts.alert.create.success', ['code' => $discount->code])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/discounts/create', [
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

        if (null === $discount = \App\Models\Discount::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.discounts.title.update'));

        $form = $this->getForm($discount)
            ->add('submit', 'submit', ['label' => __('admin.discounts.form.label.update')])
            ->setValue('pricings', $discount->pricings->pluck('id')->all())
            ->setValue('required', $discount->required->pluck('id')->all())
            ->handleRequest();

        if ($form->isSubmitted()) {
            $input = $form->getValues();
            
            if ($form->isValid()) {
                if (strtotime($input->get('start_date')) > strtotime($input->get('end_date'))) {
                    $form->setValidationError('end_date', __('admin.discounts.alert.start_after_end'));
                }

                if ('percentage' == $input->get('type') && ($input->get('amount') < 0 || $input->get('amount') > 100)) {
                    $form->setValidationError('amount', __('admin.discounts.alert.invalid_percentage'));
                }
            }

            if ($form->isValid()) {
                $discount->save();

                $discount->pricings()->sync($input->pricings);
                $discount->required()->sync($input->required);

                return redirect(adminRoute('discounts', session()->get('admin/discounts')))
                    ->with('success', view('flash/success', ['message' => __('admin.discounts.alert.update.success', ['code' => $discount->code])]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/discounts/update', [
                'form' => $form,
                'alert' => $alert ?? null,
                'discount' => $discount,
            ])
        ));
    }

    public function actionDelete($params)
    {
        if (!auth()->check('admin_settings')) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        layout()->setTitle(__('admin.discounts.title.update'));

        if (null === $discount = \App\Models\Discount::find($params['id'])) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $discount->delete();

        return redirect(adminRoute('discounts', session()->get('admin/discounts')))
            ->with('success', view('flash/success', ['message' => __('admin.discounts.alert.remove.success', ['code' => $discount->code])]));
    }

    private function getForm($model)
    {
        return form($model)
            ->add('active', 'toggle', ['label' => __('admin.discounts.form.label.published'), 'value' => '1'])
            ->add('code', 'text', ['label' => __('admin.discounts.form.label.code'), 'constraints' => 'required|alphanumeric|maxlength:120|unique:discounts,code' . (null !== $model->get($model->getPrimaryKey()) ? ',' . $model->get($model->getPrimaryKey()) : '')])
            ->add('start_date', 'date', ['label' => __('admin.discounts.form.label.start_date'), 'constraints' => 'required'])
            ->add('end_date', 'date', ['label' => __('admin.discounts.form.label.end_date'), 'constraints' => 'required'])
            ->add('type', 'select', ['label' => __('admin.discounts.form.label.type'), 'options' => ['fixed' => __('admin.discounts.form.option.fixed'), 'percentage' => __('admin.discounts.form.option.percentage')], 'constraints' => 'required'])
            ->add('amount', 'number', ['label' => __('admin.discounts.form.label.amount'), 'constraints' => 'required|min:1'])
            ->add('recurring', 'toggle', ['label' => __('admin.discounts.form.label.recurring'), 'value' => '1'])
            ->add('immutable', 'toggle', ['label' => __('admin.discounts.form.label.immutable'), 'value' => '1'])
            ->add('new_user', 'toggle', ['label' => __('admin.discounts.form.label.new_user')])
            ->add('user_limit', 'number', ['label' => __('admin.discounts.form.label.user_limit'), 'value' => '0', 'constraints' => 'required|min:0'])
            ->add('peruser_limit', 'number', ['label' => __('admin.discounts.form.label.peruser_limit'), 'value' => '0', 'constraints' => 'required|min:0'])
            ->add('pricings', 'tree', [
                'label' => __('admin.discounts.form.label.pricings'),
                'tree_source' => (new \App\Models\Product())->getTreeWithPricing(),
            ])
            ->add('required', 'tree', [
                'label' => __('admin.discounts.form.label.required_pricings'),
                'tree_source' => (new \App\Models\Product())->getTreeWithPricing(),
            ]);
    }

}
