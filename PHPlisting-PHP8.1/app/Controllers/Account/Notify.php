<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Account;

class Notify
    extends \App\Src\Mvc\BaseController
{

    public function actionNotify($params)
    {
        if (null === $gateway = \App\Models\Gateway::where('slug', $params['gateway'])->whereNotNull('active')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        try {
            $response = $gateway->getGatewayObject()->notification();
        } catch (\App\Src\Gateway\InvalidRequestException $e) {
            throw new \App\Src\Http\NotFoundHttpException();
        } catch (\App\Src\Gateway\InvalidNotificationException $e) {
            return response($e->getErrorMessage());
        }

        if (null === $transaction = \App\Models\Transaction::where('gateway_id', $gateway->id)->where('id', $response->getTransactionId())->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (false === $transaction->invoice->pricing->gateways()->get()->contains('id', $gateway->id)) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        $transaction->reference = $response->getTransactionReference();

        if ($response instanceof \App\Src\Gateway\Notification) {
            if ($response->getTransactionStatus() == \App\Src\Gateway\Notification::STATUS_COMPLETED) {
                $transaction->status = 'paid';
                $transaction->invoice->setPaid($gateway);
            } else if ($response->getTransactionStatus() != \App\Src\Gateway\Notification::STATUS_PENDING && 'pending' == $transaction->status) {
                $transaction->status = 'failed';            
            }
        } else if ($response instanceof \App\Src\Gateway\SubscriptionNotification) {
            if ($response->getTransactionStatus() == \App\Src\Gateway\SubscriptionNotification::STATUS_CREATED) {
                if ('paid' != $transaction->status) {
                    $order = $transaction->invoice->order;

                    if ('pending' == $order->status) {
                        $order->subscription_id = $response->getSubscriptionId();
                        $order->save();

                        $transaction->status = 'paid';
                        $transaction->invoice->setPaid($gateway);
                    }
                }
            } else if ($response->getTransactionStatus() == \App\Src\Gateway\SubscriptionNotification::STATUS_DELETED) {
                if ('cancelled' != $transaction->status) {
                    $order = $transaction->invoice->order;

                    if ($order->subscription_id == $response->getSubscriptionId()) {
                        $order->deactivate(false);
                        $transaction->status = 'cancelled';
                    }
                }
            }
        }

        $transaction->save();

        return response();
    }

}
