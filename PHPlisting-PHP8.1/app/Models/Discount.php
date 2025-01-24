<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Discount
    extends \App\Src\Orm\Model
{

    protected $table = 'discounts';
    protected $fillable = [
        'active',
        'code',
        'start_date',
        'end_date',
        'type',
        'amount',
        'recurring',
        'immutable',
        'new_user',
        'user_limit',
        'peruser_limit',
    ];
    protected $sortable = [
        'id' => ['id'],
        'end_date' => ['end_date'],
    ];

    public function pricings()
    {
        return $this->belongsToMany('App\Models\Pricing');
    }

    public function required()
    {
        return $this->belongsToMany('App\Models\Pricing', 'pricing_id', 'discount_id', 'discount_required');
    }

    public function listings()
    {
        return $this->hasMany('App\Models\Listing');
    }

    public function isValid($pricingId = null)
    {
        if (null === $this->active || !$this->pricings->contains('id', $pricingId)) {
            return false;
        }

        $now = new \DateTime();
        $startDate = new \DateTime($this->start_date);
        $endDate = new \DateTime($this->end_date);

        if ($now < $startDate || $now > $endDate) {
            return false;
        }

        return true;
    }

    public function delete($id = null)
    {
        $this->pricings()->detach();
        $this->required()->detach();

        return parent::delete($id);
    }

}
