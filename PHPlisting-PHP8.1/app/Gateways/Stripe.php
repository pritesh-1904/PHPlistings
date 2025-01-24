<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

// $ composer require stripe/stripe-php

namespace App\Gateways;

class Stripe
    extends \App\Src\Gateway\BaseGateway
{

    private $intervals = [
        'D' => 'day',
        'M' => 'month',
        'Y' => 'year',
    ];

    public function __construct(\App\Models\Gateway $model)
    {
        parent::__construct($model);

        try {
            $this->gateway = new \Stripe\StripeClient($this->getSettings()->get('apiKey'));
        } catch (\Stripe\Exception\InvalidArgumentException $e) {
        }
    }

    public function purchase(\App\Models\Transaction $transaction, \App\Src\Support\Collection $input = null)
    {
        $response = new \App\Src\Gateway\Response();

        try {
            $session = $this->gateway->checkout->sessions->create([
                'success_url' => route('account/checkout/success/' . $transaction->hash),
                'cancel_url' => route('account/checkout/failed/' . $transaction->hash),
                'mode' => 'subscription',
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => strtolower($transaction->currency),
                            'product_data' => [
                                'name' => $transaction->invoice->pricing->product->name,
                                'description' => strip_tags($transaction->invoice->getDescription()),
                            ],
                            'unit_amount' => $this->formatPrice($transaction->amount),
                            'recurring' => [
                                'interval' => $this->intervals[$transaction->invoice->pricing->period],
                                'interval_count' => $transaction->invoice->pricing->period_count,
                            ],
                        ],
                        'quantity' => 1,
                    ],
                ],
                'metadata' => [
                    'order_id' => $transaction->id,
                ],
                'subscription_data' => [
                    'metadata' => [
                        'order_id' => $transaction->id,
                    ],
                ],
            ]);
        } catch (\Stripe\Exception\CardException $e) {
            throw new \App\Src\Gateway\InvalidPurchaseException($e->getError()->message);
        } catch (\Stripe\Exception\RateLimitException $e) {
            throw new \App\Src\Gateway\InvalidPurchaseException('Too many requests to the API.');
        } catch (\Stripe\Exception\InvlidRequestException $e) {
            throw new \App\Src\Gateway\InvalidPurchaseException('Invalid parameters were supplied.');
        } catch (\Stripe\Exception\AuthenticationException $e) {
            throw new \App\Src\Gateway\InvalidPurchaseException('Authentication failed. Check your API keys.');
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            throw new \App\Src\Gateway\InvalidPurchaseException('Network communication with Stripe failed.');
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new \App\Src\Gateway\InvalidPurchaseException($e->getError()->message);
        }

        $response->setSuccessful();
        $response->setTransactionId($transaction->id);
        $response->setTransactionReference($session->id);

        $response->setRedirect(true);
        $response->setRedirectMethod('POST');
    
        return $response;
    }

    public function getRedirectForm(\App\Src\Form\Builder $form, $response)
    {
        layout()->addJs('<script src="https://js.stripe.com/v3/"></script>');

        layout()->addJs('
        <script>
            function checkout() 
            {
                var stripe = Stripe(\'' . $this->getSettings()->get('publicKey') . '\');

                stripe.redirectToCheckout({
                    sessionId: \'' . $response->getTransactionReference() . '\'
                })
                .then(function (result) {
                    if (result.error) {
                        alert(result.error.message);
                    }
                })
                .catch(function (error) {
                    console.error("Error:", error);
                });
            }

            checkout();

            $(document).ready(function() {
                $("#checkout").on("click", function () {
                    checkout();
                });
            });
        </script>
        ');

        $form->add('checkout', 'button', ['label' => 'Subscribe Now']);

        return $form;
    }

    public function notification()
    {
        $response = new \App\Src\Gateway\SubscriptionNotification();

        $payload = @file_get_contents('php://input');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                request()->server->get('HTTP_STRIPE_SIGNATURE'),
                $this->getSettings()->get('webhookSecret')
            );
        } catch(\UnexpectedValueException $e) {
            throw new \App\Src\Gateway\InvalidNotificationException($e->getMessage());
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            throw new \App\Src\Gateway\InvalidNotificationException($e->getMessage());
        }

        switch ($event->type) {
            case 'customer.subscription.created':
                $subscription = $event->data->object;
                
                $response->setSuccessful();
                $response->setTransactionId($subscription->metadata->order_id);
                $response->setTransactionReference($subscription->id);
                $response->setSubscriptionId($subscription->id);
                $response->setTransactionStatus(\App\Src\Gateway\SubscriptionNotification::STATUS_CREATED);

                break;

            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                
                $response->setSuccessful();
                $response->setTransactionId($subscription->metadata->order_id);
                $response->setTransactionReference($subscription->id);
                $response->setSubscriptionId($subscription->id);
                $response->setTransactionStatus(\App\Src\Gateway\SubscriptionNotification::STATUS_DELETED);

                break;
/*
            case 'checkout.session.completed':
                $session = $event->data->object;

                if ($session->payment_status == 'paid') {
                    $response->setTransactionId($session->metadata->order_id);
                    $response->setTransactionReference($session->id);
                    $response->setTransactionStatus(\App\Src\Gateway\Notification::STATUS_COMPLETED);
                }

                break;

            case 'checkout.session.async_payment_succeeded':
                $session = $event->data->object;

                $response->setTransactionId($session->metadata->order_id);
                $response->setTransactionReference($session->id);
                $response->setTransactionStatus(\App\Src\Gateway\Notification::STATUS_COMPLETED);

                break;

            case 'checkout.session.async_payment_failed':
                $session = $event->data->object;

                $response->setTransactionId($session->metadata->order_id);
                $response->setTransactionReference($session->id);
                $response->setTransactionStatus(\App\Src\Gateway\Notification::STATUS_FAILED);

                break;
*/
            
            default:

                break;
        }

        return $response;
    }

    public function getConfigurationForm(\App\Src\Form\Builder $form)
    {
        return $form
            ->add('currency', 'text', ['label' => __('admin.gateways.stripe.label.currency'), 'value' => config()->billing->currency_code])
            ->add('apiKey', 'text', ['label' => __('admin.gateways.stripe.label.apikey')])
            ->add('publicKey', 'text', ['label' => __('admin.gateways.stripe.label.publickey')])
            ->add('webhookSecret', 'text', ['label' => __('admin.gateways.stripe.label.webhooksecret')]);
    }

    public function formatPrice($value)
    {
        return number_format($value, 2, '', '');
    }

}
