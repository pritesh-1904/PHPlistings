<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Gateways;

class Authorizenet
    extends \App\Src\Gateway\BaseGateway
{

    public function __construct(\App\Models\Gateway $model)
    {
        parent::__construct($model);

        $this->gateway = \Omnipay\Omnipay::create('AuthorizeNetApi_HostedPage');

        $this->gateway->setAuthName($this->getSettings()->get('authName'));
        $this->gateway->setTransactionKey($this->getSettings()->get('transactionKey'));
        $this->gateway->setSignatureKey($this->getSettings()->get('signatureKey'));
        $this->gateway->setTestMode($this->getSettings()->get('testMode'));
    }

    public function purchase(\App\Models\Transaction $transaction, \App\Src\Support\Collection $input = null)
    {
        try {
            return $this->gateway
                ->purchase([
                    'amount' => $this->formatPrice($transaction->amount),
                    'currency' => $transaction->currency,
                    'transactionId' => $transaction->id,
                    'cancelUrl' => route('account/checkout/failed/' . $transaction->hash),
                    'returnUrl' => route('account/checkout/success/' . $transaction->hash),
                ])
                ->send();
        } catch (\Omnipay\Common\Exception\InvalidCreditCardException $e) {
            throw new \App\Src\Gateway\InvalidPurchaseException($e->getMessage());
        }
    }

    public function notification()
    {
        $response = \App\Src\Gateway\Notification();
        
        $notification = $this->gateway
            ->acceptNotification()
            ->send();
        
        if (false === $notification->isSignatureValid()) {
            throw new \App\Src\Gateway\InvalidNotificationException('Invalid signature.');
        }

        if (false === isset($notification->getData()['payload']['merchantReferenceId'])) {
            throw new \App\Src\Gateway\InvalidNotificationException('merchantReferenceId is unavailable.');
        }

        $response->setSuccessful();
        $response->setTransactionId($notification->getData()['payload']['merchantReferenceId']);
        $response->setTransactionReference($notification->getTransactionReference());

        if ($notification->getTransactionStatus() == \Omnipay\AuthorizeNetApi\Message\AcceptNotification::STATUS_COMPLETED) {
            $response->setTransactionStatus(\App\Src\Gateway\Notification::STATUS_COMPLETED);
        } else if ($notification->getTransactionStatus() == \Omnipay\AuthorizeNetApi\Message\AcceptNotification::STATUS_PENDING) {
            $response->setTransactionStatus(\App\Src\Gateway\Notification::STATUS_PENDING);
        } else {
            $response->setTransactionStatus(\App\Src\Gateway\Notification::STATUS_FAILED);
        }

        return $response;
    }

    public function getRedirectForm(\App\Src\Form\Builder $form, $response)
    {
        $form
            ->setMethod('POST')
            ->setAction($response->getRedirectUrl());

        foreach ($response->getRedirectData() as $name => $value) {
            $form->add(e($name), 'hidden', ['value' => e($value)]);
        }

        $form->add('submit', 'submit', ['label' => 'Pay Now']);

        return $form;
    }

    public function getConfigurationForm(\App\Src\Form\Builder $form)
    {
        return $form
            ->add('currency', 'text', ['label' => __('admin.gateways.authorizenet.label.currency'), 'value' => config()->billing->currency_code])
            ->add('authName', 'text', ['label' => __('admin.gateways.authorizenet.label.authname')])
            ->add('transactionKey', 'text', ['label' => __('admin.gateways.authorizenet.label.transactionkey')])
            ->add('signatureKey', 'text', ['label' => __('admin.gateways.authorizenet.label.signaturekey')])
            ->add('testMode', 'toggle', ['label' => __('admin.gateways.authorizenet.label.testmode')]);
    }

}
