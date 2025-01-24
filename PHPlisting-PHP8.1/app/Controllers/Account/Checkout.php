<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Account;

class Checkout
    extends \App\Controllers\Account\BaseController
{

    public function actionIndex($params)
    {
        if (null === $page = \App\Models\Page::where('slug', 'account/checkout')->first()) {
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

        if (null === $invoice = \App\Models\Invoice::where('id', $params['invoice'])->where('status', 'pending')->where('user_id', auth()->user()->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $gateways = $invoice->pricing->gateways()->whereNotNull('active')->get();

        $gateway = null;

        if (1 == $gateways->count() && null === session()->get('gateway')) {
            if (0 == $invoice->pricing->discounts()->count()) {
                $gateway = $gateways->first();
            } else if (null !== $invoice->order->discount_id && null !== $invoice->order->discount && null !== $invoice->order->discount->immutable) {
                $gateway = $gateways->first();
            }
        }

        $form = form()
            ->add('gateways', 'radio', [
                'label' => __('invoice.form.label.gateways'), 
                'options' => $gateways->pluck('name', 'id')->all(),
                'constraints' => 'required|maxlength:1',
            ])
            ->setValue('gateways', session()->get('gateway'))
            ->add('submit', 'submit', ['label' => __('invoice.form.label.submit')]);
        
        if ($invoice->pricing->discounts()->count() > 0) {
            if (null === $invoice->order->discount_id || (null !== $invoice->order->discount_id && null !== $invoice->order->discount && null === $invoice->order->discount->immutable)) {
                $form->add('discount', 'text', ['label' => __('listing.form.label.discount')]);
            }
        }

        $form->handleRequest();
        
        if ($form->isSubmitted()) {
            $input = $form->getValues();

            if (null !== $input->get('gateways') && isset($input->get('gateways')[0])) {
                if (false === $gateways->contains('id', $input->get('gateways')[0])) {
                    $form->setValidationError('gateways', __('invoice.alert.invalid_gateway'));
                }
            }

            if ($form->isValid()) {
                if (null === \App\Models\Gateway::find($input->get('gateways')[0])) {
                    $form->setValidationError('gateways', __('invoice.alert.invalid_gateway'));
                }
            }

            if ($form->isValid()) {
                if (null !== $input->get('discount') && '' != $input->discount) {
                    $discount = \App\Models\Discount::where('code', $input->get('discount'))->first();

                    if (null !== $discount) {
                        if (null !== $invoice->order->discount_id && null !== $invoice->order->discount && null !== $invoice->order->discount->immutable) {
                            $form->setValidationError('discount', __('discount.alert.invalid'));
                        }
                        
                        if (false === $discount->isValid($invoice->pricing->id)) {
                            $form->setValidationError('discount', __('discount.alert.invalid'));
                        }

                        if ($discount->user_limit > 0 && $discount->user_limit <= \App\Models\User::where('id', '!=', auth()->user()->id)->whereHas('orders', function ($query) use ($discount) { $query->where('discount_id', $discount->id); })->count()) {
                            $form->setValidationError('discount', __('discount.alert.user_limit_reached'));
                        }

                        if ($discount->peruser_limit > 0 && $discount->peruser_limit <= auth()->user()->orders()->where('discount_id', $discount->id)->count()) {
                            $form->setValidationError('discount', __('discount.alert.peruser_limit_reached'));
                        }

                        if ($discount->new_user == 1 && auth()->user()->listings()->count() > 0) {
                            $form->setValidationError('discount', __('discount.alert.new_user'));
                        }
                        
                        $required = $discount->required()->with('product')->get();

                        if ($required->count() > 0) {
                            if (auth()->user()->orders()->where('status', 'active')->whereIn('pricing_id', $required->pluck('id')->all())->count() < 1) {
                                $form->setValidationError('discount', __('discount.alert.required', ['pricings' => $required->pluck(function ($pricing) {
                                    return $pricing->getNameWithProduct();
                                })->implode('<br>')]));
                            }
                        }

                        if ($form->isValid()) {
                            $invoice->applyDiscount($discount);

                            if ('paid' == $invoice->status) {
                                return redirect(route('account/invoices'))
                                    ->with('success', view('flash/success', ['message' => __('invoice.alert.payment.success')]));
                            }

                            return redirect(route('account/checkout/' . $invoice->id))
                                ->with('success', view('flash/success', ['message' => __('invoice.alert.discount.success')]))
                                ->with('gateway', $input->get('gateways')[0]);
                        }
                    } else {
                        $form->setValidationError('discount', __('discount.alert.not_found'));
                    }
                }
            }

            if ($form->isValid()) {
                $gateway = \App\Models\Gateway::find($input->get('gateways')[0]);
            } else {
                $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
            }
        }

        if (null !== $gateway && null === session()->get('error')) {
            if (null !== $gateway->offsite) {
                $transaction = new \App\Models\Transaction();
                $transaction->status = 'pending';
                $transaction->gateway_id = $gateway->id;
                $transaction->amount = (null !== $gateway->subscription) ? $invoice->total : $invoice->total - $invoice->balance;
                $transaction->currency = $gateway->getGatewayObject()->getSettings()->currency;

                $invoice->transactions()->save($transaction);

                try {
                    $response = $gateway->getGatewayObject()->purchase($transaction);
                    $transaction->reference = $response->getTransactionReference();
                    $transaction->save();

                    if ('POST' == strtoupper($response->getRedirectMethod())) {
                        $form = $gateway->getGatewayObject()->getRedirectForm(form(), $response);
                    } else {
                        return redirect($response->getRedirectUrl());
                    }
                } catch (\App\Src\Gateway\InvalidPurchaseException $e) {
                    return redirect(route('account/checkout/' . $invoice->id))
                        ->with('error', view('flash/error', ['message' => $e->getErrorMessage()]));
                }
            } else {
                return redirect(route('account/checkout/' . $gateway->slug . '/' . $invoice->id));
            }
        }

        $data = collect([
            'page' => $page,
            'html' => view('account/checkout/index', [
                'invoice' => $invoice,
                'form' => $form,
                'gateway' => $gateway ?? null,
                'alert' => $alert ?? null,
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

    public function actionForm($params)
    {
        if (null === $page = \App\Models\Page::where('slug', 'account/checkout/gateway')->first()) {
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

        if (null === $invoice = \App\Models\Invoice::where('id', $params['invoice'])->where('status', 'pending')->where('user_id', auth()->user()->id)->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $gateway = \App\Models\Gateway::whereNull('offsite')->where('slug', $params['gateway'])->whereNotNull('active')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (false === $invoice->pricing->gateways()->get()->contains('id', $gateway->id)) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null !== $gateway->offline) {
            $alert = $gateway->getGatewayObject()->render();
        } else {
            $form = $gateway->getGatewayObject()->getForm(form())
                ->add('submit', 'submit', ['label' => __('invoice.form.label.submit')])
                ->handleRequest();

            if ($form->isSubmitted()) {
                $input = $form->getValues();

                if ($form->isValid()) {
                    $transaction = new \App\Models\Transaction();
                    $transaction->status = 'pending';
                    $transaction->gateway_id = $gateway->id;
                    $transaction->amount = $invoice->total - $invoice->balance;
                    $transaction->currency = $gateway->getGatewayObject()->getSettings()->currency;

                    $invoice->transactions()->save($transaction);

                    try {
                        $response = $gateway->getGatewayObject()->purchase($transaction, $input);
                        $transaction->reference = $response->getTransactionReference();

                        if (false !== $response->isSuccessful()) {
                            $invoice->setPaid($gateway);

                            $transaction->status = 'paid';
                            $transaction->save();

                            return redirect(route('account/invoices', session()->get('account/invoices')))
                                ->with('success', view('flash/success', ['message' => __('invoice.alert.payment.success')]));
                        } else if (false !== $response->isPending()) {
                            $transaction->status = 'pending';
                            $transaction->error = $response->getMessage();
                            $transaction->save();

                            return redirect(route('account/invoices', session()->get('account/invoices')))
                                ->with('success', view('flash/success', ['message' => __('invoice.alert.payment.pending')]));
                        } else if (false !== $response->isRedirect()) {
                            return redirect($response->getRedirectUrl());
                        } else {
                            $transaction->status = 'failed';
                            $transaction->error = $response->getMessage();
                            $transaction->save();

                            $alert = view('flash/error', ['message' => $response->getMessage()]);
                        }
                    } catch (\App\Src\Gateway\InvalidPurchaseException $e) {
                        $alert = view('flash/error', ['message' => $e->getErrorMessage()]);
                    }
                } else {
                    $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
                }
            }
        }

        $data = collect([
            'page' => $page,
            'html' => view('account/checkout/index', [
                'invoice' => $invoice,
                'form' => $form ?? null,
                'gateway' => $gateway ?? null,
                'alert' => $alert ?? null,
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

    public function actionSuccess($params)
    {
        if (null === $transaction = \App\Models\Transaction::where('hash', $params['transaction'])->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $gateway = $transaction->gateway) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $transaction->gateway->get('active')) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (false === $transaction->invoice->pricing->gateways()->get()->contains('id', $gateway->id)) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        try {
            $response = $gateway->getGatewayObject()->complete($transaction);

            $transaction->reference = $response->getTransactionReference();

            if (false !== $response->isSuccessful()) {            
                $transaction->invoice->setPaid($gateway);

                $transaction->status = 'paid';
                $transaction->save();

                if (null !== $gateway->subscription) {
                    $transaction->invoice->order->subscription_id = $transaction->reference();
                    $transaction->invoice->order->save();

                    if ($transaction->invoice->balance > 0) {
                        $transaction->invoice->user->account->balance = $transaction->invoice->user->account->balance + $transaction->invoice->balance;
                        $transaction->invoice->user->account->save();
                    }
                }

                return redirect(route('account/invoices', session()->get('account/invoices')))
                    ->with('success', view('flash/success', ['message' => __('invoice.alert.payment.success')]));
            } else if (false !== $response->isPending()) {
                $transaction->status = 'pending';
                $transaction->error = $response->getMessage();
                $transaction->save();

                return redirect(route('account/invoices', session()->get('account/invoices')))
                    ->with('success', view('flash/success', ['message' => __('invoice.alert.payment.pending')]));
            } else if (false !== $response->isRedirect()) {
                return redirect($response->getRedirectUrl());
            } else {
                $transaction->status = 'failed';
                $transaction->error = $response->getMessage();
                $transaction->save();

                return redirect(route('account/invoices'))
                    ->with('error', view('flash/error', ['message' => $response->getMessage()]));
            }
        } catch (\App\Src\Gateway\InvalidRequestException $e) {
            return redirect(route('account/invoices', session()->get('account/invoices')))
                ->with('success', view('flash/success', ['message' => __('invoice.alert.payment.pending')]));
        } catch (\App\Src\Gateway\InvalidCompleteException $e) {
            return redirect(route('account/invoices', session()->get('account/invoices')))
                ->with('error', view('flash/error', ['message' => $e->getErrorMessage()]));
        }
    }

    public function actionCancel($params)
    {
        if (null === $transaction = \App\Models\Transaction::where('hash', $params['transaction'])->where('status', 'pending')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $gateway = $transaction->gateway) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (null === $transaction->gateway->get('active')) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (false === $transaction->invoice->pricing->gateways()->get()->contains('id', $gateway->id)) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $transaction->status = 'cancelled';
        $transaction->save();

        return redirect(route('account/invoices'))
            ->with('success', view('flash/success', ['message' => __('invoice.alert.payment.cancelled')]));
    }

}
