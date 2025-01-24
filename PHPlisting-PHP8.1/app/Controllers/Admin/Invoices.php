<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Admin;

class Invoices
    extends \App\Controllers\Admin\BaseController
{

    public function actionIndex($params)
    {
        if (false === auth()->check(['admin_content', 'admin_listings'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.invoices.title.index', ['singular' => $type->name_singular, 'plural' => $type->name_plural]));

        if (null !== request()->post->get('action') && null !== request()->post->get('id')) {
            switch (request()->post->action) {
                case 'pay':
                    $invoices = \App\Models\Invoice::whereIn('id', (array) request()->post->id)
                        ->where('status', 'pending')
                        ->get();

                    foreach ($invoices as $invoice) {
                        $invoice->setPaid();
                    }

                    $alert = view('flash/success', ['message' => __('admin.invoices.alert.pay.success')]);
                    break;
            }
        }

        if (null === request()->get->get('sort')) {
            request()->get->sort = 'id';
            request()->get->sort_direction = 'desc';
        }

        $query = \App\Models\Invoice::search(null, [], 'admin/' . $type->slug . '-invoices')
            ->where('type_id', $type->id)
            ->whereHas('type', function ($query) {
                $query->whereNull('deleted');
            })
            ->with([
                'order.listing',
                'pricing'
            ]);

        if (null !== request()->get->get('listing_id')) {
            $query->whereHas('order', function ($query) {
                $query->where('listing_id', request()->get->get('listing_id'));
            });
        }

        if (false !== config()->compat->hide_free_pricing_invoices) {
            $query->where('subtotal', '>', 0);
        }

        $form = form()
            ->setMethod('get')
            ->setTemplate('form/inline')
            ->add('status', 'select', [
                'options' => ['' => __('admin.invoices.searchform.label.status')] + ['paid' => __('status.label.paid'), 'pending' => __('status.label.pending'), 'cancelled' => __('status.label.cancelled')],
                'label' => __('admin.invoices.searchform.label.status'),
                'weight' => 10,
            ])
            ->add('listing_id', 'listing', [
                'placeholder' => __('admin.invoices.searchform.placeholder.listing'),
                'type' => $type->id,
                'weight' => 30,
            ])
            ->add('submit', 'submit', [
                'label' => __('admin.invoices.searchform.label.submit')
            ])
            ->forceRequest();

        $table = dataTable($query->paginate())
            ->addColumns([
                'status' => [__('admin.invoices.datatable.label.status'), function ($invoice) {
                    return view('misc/status', [
                        'type' => 'invoice',
                        'status' => $invoice->status,
                    ]);
                }],
                'title' => [__('admin.invoices.datatable.label.title'), function ($invoice) {
                    return e($invoice->order->listing->title);
                }],
                'product' => [__('admin.invoices.datatable.label.product'), function ($invoice) {
                    if (null !== $invoice->pricing_id && null !== $invoice->pricing) {
                        return $invoice->pricing->getNameWithProduct();
                    }
                }],
                'total' => [__('admin.invoices.datatable.label.total'), function ($invoice) {
                    return locale()->formatPrice($invoice->total);
                }],
                'added_datetime' => [__('admin.invoices.datatable.label.added_datetime'), function ($invoice) {
                    return locale()->formatDatetime($invoice->added_datetime, auth()->user()->timezone);
                }],
            ])
            ->orderColumns([
                'added_datetime',
            ])
            ->addActions([
                'pay' => [__('admin.invoices.datatable.action.pay'), function ($invoice) use ($type) {
                    if ('pending' == $invoice->status) {
                        return adminRoute($type->slug . '-invoices/pay/' . $invoice->id);
                    }
                }],
                'view' => [__('admin.invoices.datatable.action.view'), function ($invoice) use ($type) {
                    return adminRoute($type->slug . '-invoices/view/' . $invoice->id);
                }],
                'listing' => [__('admin.invoices.datatable.action.listing'), function ($invoice) use ($type) {
                    return adminRoute('manage/' . $type->slug . '/summary/' . $invoice->order->listing_id);
                }],
            ])
            ->addBulkActions([
                'pay' => __('admin.invoices.datatable.bulkaction.pay'),
            ]);

        return response(layout()->content(
            view('admin/invoices/index', [
                'type' => $type,
                'form' => $form,
                'listing' => (null !== request()->get->get('listing_id') ? \App\Models\Listing::find(request()->get->get('listing_id')) : null),
                'invoices' => $table,
                'alert' => $alert ?? null
            ])
        )); 
    }

    public function actionView($params)
    {
        if (false === auth()->check(['admin_content', 'admin_listings'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $invoice = \App\Models\Invoice::where('id', $params['id'])->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()->setTitle(__('admin.invoices.title.view', ['singular' => $type->name_singular, 'plural' => $type->name_plural, 'id' => $invoice->id]));

        if (null === request()->get->get('sort')) {
            request()->get->put('sort', 'id');
            request()->get->put('sort_direction', 'desc');
        }

        $table = dataTable($invoice->transactions()->orderBy('id', 'desc')->get())
            ->addColumns([
                'status' => [__('admin.transactions.datatable.label.status'), function ($transaction) {
                    return view('misc/status', [
                        'type' => 'transaction',
                        'status' => $transaction->status,
                    ]);
                }],
                'gateway' => [__('admin.transactions.datatable.label.gateway'), function ($transaction) {
                    return $transaction->gateway->name;
                }],
                'currency' => [__('admin.transactions.datatable.label.currency')],
                'amount' => [__('admin.transactions.datatable.label.amount')],
                'added_datetime' => [__('admin.transactions.datatable.label.added'), function($transaction) {
                    return locale()->formatDatetime($transaction->added_datetime, auth()->user()->timezone);
                }],
            ]);

        return response(layout()->content(
            view('admin/invoices/view', [
                'type' => $type,
                'invoice' => $invoice,
                'transactions' => $table,
            ])
        ));
    }

    public function actionPay($params)
    {
        if (false === auth()->check(['admin_content', 'admin_listings'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $invoice = \App\Models\Invoice::where('id', $params['id'])->where('type_id', $type->id)->where('status', 'pending')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $invoice->setPaid();

        return redirect(adminRoute($type->slug . '-invoices', session()->get('admin/' . $type->slug . '-invoices')))
            ->with('success', view('flash/success', ['message' => __('admin.invoices.alert.pay.success')]));
    }

    public function actionPrint($params)
    {
        if (false === auth()->check(['admin_content', 'admin_listings'])) {
            return redirect(adminRoute(''))
                ->with('error', view('flash/error', ['message' => [__('admin.alert.permission_denied')]]));
        }

        if (null === $type = \App\Models\Type::where('slug', $params['type'])->whereNull('deleted')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $invoice = \App\Models\Invoice::where('id', $params['id'])->where('type_id', $type->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $pdf = new \Dompdf\Dompdf();

        $options = $pdf->getOptions();
        $options->setIsRemoteEnabled(true);
        $pdf->setOptions($options);

        $pdf->loadHtml(view('admin/invoices/print', [
            'invoice' => $invoice,
        ]));

        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        return response($pdf->output())
            ->withHeaders([
                'Content-type' => 'application/pdf',
            ]);
    }

}
