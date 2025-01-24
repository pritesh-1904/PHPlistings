<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Account;

class Invoices
    extends \App\Controllers\Account\BaseController
{

    public function actionIndex($params)
    {
        if (null === $page = \App\Models\Page::where('slug', 'account/invoices')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ((null !== config()->general->maintenance || null === $page->active) && false === auth()->check(['admin_login', 'admin_appearance'])) {
            return redirect(route('maintenance'), 302);
        }

        layout()
            ->setTitle($page->title)
            ->setMeta('title', $page->meta_title)
            ->setMeta('keywords', $page->meta_keywords)
            ->setMeta('description', $page->meta_description);

        if (null === request()->get->get('sort')) {
            request()->get->put('sort', 'id');
            request()->get->put('sort_direction', 'desc');
        }

        $query = \App\Models\Invoice::search()
            ->whereHas('type', function ($query) {
                $query->whereNull('deleted');

                if (false === auth()->check('admin_login')) {
                    $query->whereNotNull('active');
                }
            })
            ->with([
                'order.listing',
                'pricing'
            ])
            ->where('user_id', auth()->user()->id);

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
                'options' => ['' => __('invoice.searchform.label.status')] + ['paid' => __('status.label.paid'), 'pending' => __('status.label.pending'), 'cancelled' => __('status.label.cancelled')],
                'label' => __('invoice.searchform.label.status'),
                'weight' => 10,
            ])
            ->add('listing_id', 'listing', [
                'placeholder' => __('invoice.searchform.placeholder.listing'),
                'weight' => 20,
            ])
            ->add('submit', 'submit', [
                'label' => __('invoice.searchform.label.submit')
            ])
            ->forceRequest();

        $table = dataTable($query->paginate())
            ->addColumns([
                'status' => [__('invoice.datatable.label.status'), function ($invoice) {
                    return view('misc/status', [
                        'type' => 'invoice',
                        'status' => $invoice->status,
                    ]);
                }],
                'title' => [__('invoice.datatable.label.title'), function ($invoice) {
                    return e($invoice->order->listing->title);
                }],
                'product' => [__('invoice.datatable.label.product'), function ($invoice) {
                    if (null !== $invoice->pricing_id && null !== $invoice->pricing) {
                        return $invoice->pricing->getNameWithProduct();
                    }
                }],
                'added_datetime' => [__('invoice.datatable.label.added_datetime'), function ($invoice) {
                    return locale()->formatDatetime($invoice->added_datetime, auth()->user()->timezone);
                }],
            ])
            ->orderColumns([
                'added_datetime',
            ])
            ->addActions([
                'view' => [__('invoice.datatable.action.view'), function ($invoice) {
                    return route('account/invoices/' . $invoice->id);
                }],
                'pay' => [__('invoice.datatable.action.pay'), function ($invoice) {
                    if ('pending' == $invoice->status) {
                        return route('account/checkout/' . $invoice->id);
                    }
                }],
            ]);

        $data = collect([
            'page' => $page,
            'html' => view('account/invoices/index', [
                'form' => $form,
                'invoices' => $table,
                'listing' => (null !== request()->get->get('listing_id') ? \App\Models\Listing::where('id', request()->get->get('listing_id'))->where('user_id', auth()->user()->id)->first() : null),
            ]),
        ]);

        $response = $page->render($data);

        if ($response instanceof \App\Src\Http\RedirectResponse) {
            return $response;
        }

        return response(
            layout()->content($response)
        );
    }

    public function actionView($params)
    {
        if (null === $page = \App\Models\Page::where('slug', 'account/invoices/view')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ((null !== config()->general->maintenance || null === $page->active) && false === auth()->check(['admin_login', 'admin_appearance'])) {
            return redirect(route('maintenance'), 302);
        }

        layout()
            ->setTitle($page->title)
            ->setMeta('title', $page->meta_title)
            ->setMeta('keywords', $page->meta_keywords)
            ->setMeta('description', $page->meta_description);

        if (null === $invoice = auth()->user()->invoices()->where('id', $params['id'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === request()->get->get('sort')) {
            request()->get->put('sort', 'id');
            request()->get->put('sort_direction', 'desc');
        }

        $table = dataTable($invoice->transactions()->orderBy('id', 'desc')->get())
            ->addColumns([
                'status' => [__('transaction.datatable.label.status'), function ($transaction) {
                    return view('misc/status', [
                        'type' => 'transaction',
                        'status' => $transaction->status,
                    ]);
                }],
                'gateway' => [__('transaction.datatable.label.gateway'), function ($transaction) {
                    return $transaction->gateway->name;
                }],
                'currency' => [__('transaction.datatable.label.currency')],
                'amount' => [__('transaction.datatable.label.amount')],
                'added_datetime' => [__('transaction.datatable.label.added'), function($transaction) {
                    return locale()->formatDatetime($transaction->added_datetime, auth()->user()->timezone);
                }],
            ]);

        $data = collect([
            'page' => $page,
            'html' => view('account/invoices/view', [
                'invoice' => $invoice,
                'transactions' => $table,
            ]),
        ]);

        $response = $page->render($data);

        if ($response instanceof \App\Src\Http\RedirectResponse) {
            return $response;
        }

        return response(
            layout()->content($response)
        );
    }

    public function actionPrint($params)
    {
        if (null === $page = \App\Models\Page::where('slug', 'account/invoices/view')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ((null !== config()->general->maintenance || null === $page->active) && false === auth()->check(['admin_login', 'admin_appearance'])) {
            return redirect(route('maintenance'), 302);
        }

        if (null === $invoice = auth()->user()->invoices()->where('id', $params['id'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $pdf = new \Dompdf\Dompdf();

        $options = $pdf->getOptions();
        $options->setIsRemoteEnabled(true);
        $pdf->setOptions($options);

        $pdf->loadHtml(view('account/invoices/print', [
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
