<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Pricing
    extends \App\Src\Orm\Model
{
    use \App\Src\Orm\Traits\SortableTrait;

    protected $table = 'pricings';
    protected $fillable = [
        'hidden',
        'autoapprovable',
        'claimable',
        'cancellable',
        'period',
        'period_count',
        'price',
        'user_limit',
        'peruser_limit',
    ];
    protected $sortable = [
        'weight' => ['weight'],
    ];
    protected $searchable = [
        'product_id' => ['product_id', 'eq'],
    ];

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function discounts()
    {
        return $this->belongsToMany('App\Models\Discount');
    }

    public function gateways()
    {
        return $this->belongsToMany('App\Models\Gateway');
    }

    public function claims()
    {
        return $this->hasMany('App\Models\Claim');
    }

    public function upgrades()
    {
        return $this->belongsToMany('App\Models\Pricing', 'upgrade_id', 'pricing_id', 'pricing_upgrade');
    }
    
    public function revUpgrades()
    {
        return $this->belongsToMany('App\Models\Pricing', 'pricing_id', 'upgrade_id', 'pricing_upgrade');
    }

    public function required()
    {
        return $this->belongsToMany('App\Models\Pricing', 'required_id', 'pricing_id', 'pricing_required');
    }
    
    public function revRequired()
    {
        return $this->belongsToMany('App\Models\Pricing', 'pricing_id', 'required_id', 'pricing_required');
    }

    public function getName()
    {
        $price = locale()->formatPrice($this->price);
        
        return __('pricing.name.' . strtolower($this->period), ['duration' => $this->period_count, 'price' => (0 < (int) $this->price ? $price : __('widget.pricing.label.free'))], $this->period_count);
    }

    public function getNameWithProduct()
    {
        return __('pricing.name.withproduct', ['product' => $this->product->name, 'pricing' => $this->getName()]);
    }

    public function getNameWithProductAndType()
    {
        return __('pricing.name.withproductandtype', ['type' => $this->product->listingType->name_plural, 'product' => $this->product->name, 'pricing' => $this->getName()]);
    }

    public function getNameWithoutPrice()
    {
        return __('pricing.name.noprice.' . strtolower($this->period), ['duration' => $this->period_count], $this->period_count);
    }

    public function getGatewayTree()
    {
        $tree = [];

        foreach ($this->gateways()->whereNotNull('active')->orderBy('weight')->get() as $gateway) {
            $tree[] = ['key' => $gateway->id, 'title' => $gateway->name];
        }

        return $tree;
    }

    public function delete($id = null)
    {
        $this->unsort();

        $this->discounts()->detach();
        $this->upgrades()->detach();
        $this->required()->detach();
        $this->gateways()->detach();

        return parent::delete($id);
    }

}
