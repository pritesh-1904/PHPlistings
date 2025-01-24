<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Gateways;

class Paypal
    extends \App\Src\Gateway\BaseGateway
{

    public function __construct(\App\Models\Gateway $model)
    {
        parent::__construct($model);

        $class = '\PayPalCheckoutSdk\Core\\' . (null === $this->getSettings()->testMode ? 'ProductionEnvironment' : 'SandboxEnvironment');

        $environment = new $class(
            $this->getSettings()->clientId,
            $this->getSettings()->secret
        );

        $this->gateway = new \PayPalCheckoutSdk\Core\PayPalHttpClient($environment);
    }

    public function purchase(\App\Models\Transaction $transaction, \App\Src\Support\Collection $input = null)
    {
        $request = new \PayPalCheckoutSdk\Orders\OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $transaction->id,
                    'amount' => [
                        'currency_code' => $transaction->currency,
                        'value' => $this->formatPrice($transaction->amount),
                    ]
                ]
            ],
            'application_context' => [
                'return_url' => route('account/checkout/success/' . $transaction->hash),
                'cancel_url' => route('account/checkout/failed/' . $transaction->hash),
                'locale' => 'en-US',
                'landing_page' => 'BILLING',
                'user_action' => 'PAY_NOW',
            ]
        ];

        $res = new \App\Src\Gateway\Response();

        try {
            $response = $this->gateway->execute($request);

            if ('201' == $response->statusCode) {
                if ('CREATED' == $response->result->status && 'CAPTURE' == $response->result->intent) {
                    $res->setSuccessful();
                    $res->setTransactionId($transaction->id);
                    $res->setTransactionReference($response->result->id);

                    foreach ($response->result->links as $link) {
                        if ('approve' == $link->rel) {
                            $res->setRedirect(true);
                            $res->setRedirectUrl($link->href);
                            $res->setRedirectMethod('GET');
                        }
                    }
                }            
            } else {
                $res->setMessage('Invalid Response Status: ' . $response->statusCode);
            }            
        } catch (\PayPalHttp\HttpException $e) {
            throw new \App\Src\Gateway\InvalidPurchaseException($e->getMessage());
        }        

        return $res;
    }

    public function complete(\App\Models\Transaction $transaction)
    {
        $res = new \App\Src\Gateway\Response();

        try {
            $request = new \PayPalCheckoutSdk\Orders\OrdersCaptureRequest($transaction->reference);

            $response = $this->gateway->execute($request);

            if ('201' == $response->statusCode) {
                if ('COMPLETED' == $response->result->status && $response->result->id == $transaction->reference) {
                    $res->setSuccessful();

                    foreach ($response->result->purchase_units as $unit) {
                        foreach ($unit->payments->captures as $capture) {
                            $res->setTransactionReference($capture->id);
                        }
                    }
                }
            }
        } catch (\PayPalHttp\HttpException $e) {
            throw new \App\Src\Gateway\InvalidCompleteException($e->getMessage());
        }        

        return $res;
    }

    public function getConfigurationForm(\App\Src\Form\Builder $form)
    {
        return $form
            ->add('currency', 'text', ['label' => __('admin.gateways.paypal.label.currency'), 'value' => config()->billing->currency_code])
            ->add('clientId', 'text', ['label' => __('admin.gateways.paypal.label.clientid')])
            ->add('secret', 'text', ['label' => __('admin.gateways.paypal.label.secret')])
            ->add('testMode', 'toggle', ['label' => __('admin.gateways.paypal.label.testmode')]);
    }

}
