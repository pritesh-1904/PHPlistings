<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Invoice
    extends \App\Src\Orm\Model
{

    protected $table = 'invoices';
    protected $fillable = [
        'status',
        'subtotal',
        'total',
        'balance',
        'tax',
        'discount',
    ];
    protected $searchable = [
        'status' => ['status', 'eq'],
        'user_id' => ['user_id', 'eq'],
    ];
    protected $sortable = [
        'id' => ['id'],
        'status' => ['status'],
        'added_datetime' => ['added_datetime'],
        'paid_datetime' => ['paid_datetime'],
    ];

    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction');
    }
    
    public function gateway()
    {
        return $this->belongsTo('App\Models\Gateway');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\Type');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function pricing()
    {
        return $this->belongsTo('App\Models\Pricing');
    }

    public function getDescription()
    {
        return view('misc/invoice', [
            'invoice' => $this,
        ]);
    }

    public function changeUser($id)
    {
        if ($id != $this->user_id) {
            $this->user_id = $id;
        }

        return $this;
    }

    public function applyDiscount(\App\Models\Discount $discount)
    {
        $this->order->discount_id = $discount->id;

        $subtotal = $this->order->price;
        $discountValue = 0;
        $tax = 0;

        if ($discount->isValid($this->order->pricing_id)) {
            if ($discount->type == 'percentage') {
                 $discountValue = $subtotal / 100 * $discount->amount;
            } else {
                $discountValue = $discount->amount;
            }
        }

        if (null !== $this->order->user->taxable) {
            $tax = $this->order->calculateTax($subtotal - $discountValue);

            if ('inclusive' == config()->billing->tax) {
                $subtotal = $subtotal - $tax;
            }
        }

        $total = $subtotal - $discountValue + $tax;

        if ($total < 0) {
            $total = 0;
        }

        $this->status = ($total == 0 ? 'paid' : 'pending');
        $this->total = $total;
        $this->discount = $discountValue;
        $this->tax = $tax;

        if ($this->balance > 0) {
            $this->order->user->account->balance = $this->order->user->account->balance + $this->balance;
            $this->order->user->account->save();

            $this->balance = 0;
        }

        if (null === $this->order->subscription_id && $total > 0 && $this->user->account->balance > 0) {
            if ($this->order->user->account->balance >= $total) {
                $this->balance = $total;
                $this->user->account->balance = $this->user->account->balance - $total;
                $this->status = 'paid';
            } else {
                $invoice->balance = $this->user->account->balance;
                $this->user->account->balance = 0;
            }

            $this->order->user->account->save();
        }

        if ($this->status == 'paid') {
            $this->paid_datetime = date('Y-m-d H:i:s');
        }

        $this->order->save();

        return $this->save();
    }

    public function setPaid(\App\Models\Gateway $gateway = null, $sendEmailNotification = true)
    {
        $this->status = 'paid';

        if (null !== $gateway) {
            $this->gateway_id = $gateway->id;
            
            if (null !== $gateway->subscription) {
                if ($this->balance > 0) {
                    $this->user->account->balance = $this->user->account->balance + $this->balance;
                    $this->user->account->save();

                    $this->balance = 0;
                }
            }
        }

        $this->paid_datetime = date('Y-m-d H:i:s');

        if (false !== $sendEmailNotification && null !== $this->order->listing->type->get('active')) {
            (new \App\Repositories\EmailQueue())->push(
                'user_invoice_paid',
                $this->user->id,
                [
                    'id' => $this->user->id,
                    'first_name' => $this->user->first_name,
                    'last_name' => $this->user->last_name,
                    'email' => $this->user->email,

                    'invoice_id' => $this->id,
                    'invoice_subtotal' => locale()->formatPrice($this->subtotal),
                    'invoice_total' => locale()->formatPrice($this->total),
                    'invoice_balance' => locale()->formatPrice($this->balance),
                    'invoice_tax' => locale()->formatPrice($this->tax),
                    'invoice_discount' => locale()->formatPrice($this->discount),

                    'listing_id' => $this->order->listing->id,
                    'listing_title' => $this->order->listing->title,
                    'listing_type_singular' => $this->order->listing->type->name_singular,
                    'listing_type_plural' => $this->order->listing->type->name_plural,
                    
                    'invoice_product' => $this->pricing->getNameWithProduct(),
                    'invoice_addeddate' => locale()->formatDatetime($this->added_datetime, $this->user->timezone),
                    'invoice_paiddate' => locale()->formatDatetime($this->paid_datetime, $this->user->timezone),

                    'link' => route('account/invoices/' . $this->id),
                ],
                [$this->user->email => $this->user->getName()],
                [config()->email->from_email => config()->email->from_name]
            );
        }

        return $this->save();
    }

    public function setPaidWithoutEmailNotification(\App\Models\Gateway $gateway = null) {
        return $this->setPaid($gateway, false);
    }

    public function getBillingCycleEndDatetime($startDatetime = null)
    {
        return (new \DateTime($startDatetime ?? date('Y-m-d H:i:s')))
            ->add(new \DateInterval('P' . $this->period_count . strtoupper($this->period)))
            ->format('Y-m-d H:i:s');
    }

    public function getDueDatetime()
    {
        return (new \DateTime('now'))
            ->add(new \DateInterval('P' . config()->billing->invoice_creation_days . 'D'))
            ->format('Y-m-d H:i:s');
    }

    public function delete($id = null)
    {
        $this->transactions()->delete();
        
        return parent::delete($id);
    }

}
