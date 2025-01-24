<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

// $ composer require mollie/mollie-api-php:^2.0

namespace App\Gateways;

class Mollie
    extends \App\Src\Gateway\BaseGateway
{

    public function __construct(\App\Models\Gateway $model)
    {
        parent::__construct($model);

        try {
            $this->gateway = new \Mollie\Api\MollieApiClient();
            $this->gateway->setApiKey($this->getSettings()->get('apiKey'));
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
        }
    }

    public function purchase(\App\Models\Transaction $transaction, \App\Src\Support\Collection $input = null)
    {
        $response = new \App\Src\Gateway\Response();

        try {
            $payment = $this->gateway->payments->create([
                'amount' => [
                    'currency' => $transaction->currency,
                    'value' => (string) $this->formatPrice($transaction->amount),
                ],
                'description' => strip_tags($transaction->invoice->getDescription()),
                'redirectUrl' => route('account/checkout/success/' . $transaction->hash),
                'webhookUrl' => route('account/checkout/notify/mollie'),
                'metadata' => [
                    'order_id' => $transaction->id,
                ],
            ]);
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            throw new \App\Src\Gateway\InvalidPurchaseException($e->getMessage());
        }

        $response->setSuccessful();
        $response->setTransactionId($transaction->id);
        $response->setTransactionReference($payment->id);
        $response->setRedirect(true);
        $response->setRedirectUrl($payment->getCheckoutUrl());
        $response->setRedirectMethod('GET');
    
        return $response;
    }

    public function notification()
    {
        $response = new \App\Src\Gateway\Notification();

        try {
            $payment = $this->gateway->payments->get(request()->post->get('id'));

            $response->setSuccessful();
            $response->setTransactionId($payment->metadata->order_id);
            $response->setTransactionReference(request()->post->get('id'));

            if ($payment->isPaid() && false === $payment->hasRefunds() && false === $payment->hasChargebacks()) {
                $response->setTransactionStatus(\App\Src\Gateway\Notification::STATUS_COMPLETED);
            } else if ($payment->isPending()) {
                $response->setTransactionStatus(\App\Src\Gateway\Notification::STATUS_PENDING);
            } else {
                $response->setTransactionStatus(\App\Src\Gateway\Notification::STATUS_FAILED);
            }
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            throw new \App\Src\Gateway\InvalidNotificationException($e->getMessage());
        }

        return $response;
    }

    public function getConfigurationForm(\App\Src\Form\Builder $form)
    {
        return $form
            ->add('currency', 'text', ['label' => __('admin.gateways.mollie.label.currency'), 'value' => config()->billing->currency_code])
            ->add('apiKey', 'text', ['label' => __('admin.gateways.mollie.label.apikey')]);
    }

}
