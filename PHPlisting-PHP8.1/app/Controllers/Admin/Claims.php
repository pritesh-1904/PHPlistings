<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Claims
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (!auth()->check(['admin_content', 'admin_claims'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.claims.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));
        
        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'desc';
        }

        $claims = \App\Models\Claim::search(null, [], 'admin/' . $type->slug . '-claims')
            ->where('type_id', $type->id)
            ->with([
                'listing',
                'user',
                'pricing.product',
            ])
            ->paginate();

        $table = dataTable($claims)
            ->addColumns([
                'id' => [__('admin.claims.datatable.label.id')],
                'status' => [__('admin.claims.datatable.label.status'), function ($claim) {
                    return view('misc/status', [
                        'type' => 'claim',
                        'status' => $claim->status,
                    ]);
                }],
                'user' => [__('admin.claims.datatable.label.user'), function ($claim) {
                    return $claim->user->getName() . ' (id: ' . $claim->user->id . ')';
                }],
                'listing' => [__('admin.claims.datatable.label.listing'), function ($claim) {
                    return $claim->listing->title;
                }],
                'pricing' => [__('admin.claims.datatable.label.pricing'), function ($claim) {
                    if (null !== $claim->pricing_id && null !== $claim->pricing) {
                        return $claim->pricing->getNameWithProduct();
                    }
                }],
                'added_datetime' => [__('admin.claims.datatable.label.added_datetime'), function($claim) {
                    return locale()->formatDatetimeDiff($claim->added_datetime);
                }],
            ])
            ->addActions([
                'approve' => [__('admin.claims.datatable.action.approve'), function ($claim) use ($type) {
                    if (null === $claim->pricing_id || null === $claim->pricing) {
                        return null;
                    }
                    
                    if (in_array($claim->status, ['approved', 'rejected'])) {
                        return null;
                    }

                    if (null !== $claim->listing->get('claimed')) {
                        return null;
                    }

                    return adminRoute($type->slug . '-claims/approve/' . $claim->id);
                }],
                'reject' => [__('admin.claims.datatable.action.reject'), function ($claim) use ($type) {
                    if (in_array($claim->status, ['approved', 'rejected'])) {
                        return null;
                    }

                    return adminRoute($type->slug . '-claims/reject/' . $claim->id);
                }],
                'update' => [__('admin.claims.datatable.action.update'), function ($claim) use ($type) {
                    if (in_array($claim->status, ['approved', 'rejected'])) {
                        return null;
                    }

                    if (null !== $claim->listing->get('claimed')) {
                        return null;
                    }

                    return adminRoute($type->slug . '-claims/update/' . $claim->id);
                }],
                'listing' => [__('admin.claims.datatable.action.listing'), function ($claim) use ($type) {
                    return adminRoute('manage/' . $type->slug . '/summary/' . $claim->listing_id);
                }],
            ])
            ->orderColumns([
                'added_datetime',
            ]);

        $form = form()
            ->setMethod('get')
            ->setTemplate('form/inline')
            ->add('listing_id', 'listing', [
                'placeholder' => __('admin.claims.searchform.placeholder.listing'),
                'type' => $type->id,
                'weight' => 10
            ])
            ->add('submit', 'submit', [
                'label' => __('admin.claims.searchform.label.submit')
            ])
            ->forceRequest();

        return response(layout()->content(
            view('admin/claims/index', [
                'type' => $type,
                'form' => $form,
                'listing' => (null !== request()->get->get('listing_id') ? \App\Models\Listing::find(request()->get->get('listing_id')) : null),
                'claims' => $table,
            ])
        ));
    }

    public function actionUpdate($params)
    {
        if (!auth()->check(['admin_content', 'admin_claims'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $claim = \App\Models\Claim::where('id', $params['id'])->where('status', 'pending')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.claims.title.update', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        $form = form($claim)
            ->add('listing', 'ro', ['label' => __('admin.claims.form.label.listing'), 'value' => $claim->listing->title])
            ->add('user', 'ro', ['label' => __('admin.claims.form.label.user'), 'value' => $claim->user->getName()])
            ->add('comment', 'textarea', ['label' => __('admin.claims.form.label.comment')])
            ->add('pricing_id', 'tree', [
                'label' => __('admin.claims.form.label.pricing'),
                'tree_source' => (new \App\Models\Product())->getTreeWithHiddenPricing($claim->listing->type_id),
                'constraints' => 'required|maxlength:1',
            ])
            ->add('submit', 'submit', ['label' => __('admin.claims.form.label.update')])
            ->handleRequest();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $claim->pricing_id = $form->getValues()->pricing_id[0];
                $claim->save();

                return redirect(adminRoute($type->slug . '-claims', session()->get('admin/' . $type->slug . '-claims')))
                    ->with('success', view('flash/success', ['message' => __('admin.claims.alert.update.success')]));
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        return response(layout()->content(
            view('admin/claims/update', [
                'type' => $type,
                'form' => $form,
                'alert' => $alert ?? null
            ])
        ));
    }

    public function actionApprove($params)
    {
        if (!auth()->check(['admin_content', 'admin_claims'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $claim = \App\Models\Claim::where('id', $params['id'])->where('type_id', $type->id)->where('status', 'pending')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $pricing = \App\Models\Pricing::where('id', $claim->pricing_id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null !== $claim->listing->get('claimed')) {
            return redirect(adminRoute($type->slug . '-claims', session()->get('admin/' . $type->slug . '-claims')))
                ->with('error', view('flash/error', ['message' => __('admin.claims.alert.already_claimed')]));
        }

        $claim->status = 'approved';
        $claim->updated_datetime = date('Y-m-d H:i:s');
        $claim->save();

        $claim->listing->claimed = 1;
        $claim->listing->changeUser($claim->user->id);
        $claim->listing->order->activate($claim->pricing_id, false);
        $claim->listing->save();

        foreach ($claim->listing->order->invoices() as $invoice) {
            if ($claim->listing->order->invoice_id != $invoice->id) {
                $invoice->delete();
            }
        }

        if (null !== $type->get('active')) {
            (new \App\Repositories\EmailQueue())->push(
                'user_listing_claim_approved',
                $claim->user->id,
                [
                    'id' => $claim->user->id,
                    'first_name' => $claim->user->first_name,
                    'last_name' => $claim->user->last_name,
                    'email' => $claim->user->email,

                    'listing_id' => $claim->listing->id,
                    'listing_title' => $claim->listing->title,
                    'listing_type_singular' => $type->name_singular,
                    'listing_type_plural' => $type->name_plural,
                ],
                [$claim->user->email => $claim->user->getName()],
                [config()->email->from_email => config()->email->from_name]
            );
        }

        return redirect(adminRoute($type->slug . '-claims', session()->get('admin/' . $type->slug . '-claims')))
            ->with('success', view('flash/success', ['message' => __('admin.claims.alert.approve.success')]));
    }

    public function actionReject($params)
    {
        if (!auth()->check(['admin_content', 'admin_claims'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $claim = \App\Models\Claim::where('id', $params['id'])->where('type_id', $type->id)->where('status', 'pending')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $claim->status = 'rejected';
        $claim->updated_datetime = date('Y-m-d H:i:s');
        $claim->save();

        if (null !== $type->get('active')) {
            (new \App\Repositories\EmailQueue())->push(
                'user_listing_claim_rejected',
                $claim->user->id,
                [
                    'id' => $claim->user->id,
                    'first_name' => $claim->user->first_name,
                    'last_name' => $claim->user->last_name,
                    'email' => $claim->user->email,

                    'listing_id' => $claim->listing->id,
                    'listing_title' => $claim->listing->title,
                    'listing_type_singular' => $type->name_singular,
                    'listing_type_plural' => $type->name_plural,
                ],
                [$claim->user->email => $claim->user->getName()],
                [config()->email->from_email => config()->email->from_name]
            );
        }

        return redirect(adminRoute($type->slug . '-claims', session()->get('admin/' . $type->slug . '-claims')))
            ->with('success', view('flash/success', ['message' => __('admin.claims.alert.reject.success')]));
    }

}
