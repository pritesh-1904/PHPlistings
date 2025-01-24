<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Gateway
    extends \App\Src\Orm\Model
{
    use \App\Src\Orm\Traits\SortableTrait;

    protected $table = 'gateways';
    protected $fillable = [
        'active',
        'name',
        'description',
    ];
    protected $translatable = [
        'name',
        'description',
    ];
    protected $sortable = [
        'weight',
    ];
    protected $gateway;

    public function pricings()
    {
        return $this->belongsToMany('App\Models\Pricing');
    }

    public function getTree()
    {
        $tree = [];

        foreach ($this->getQuery()->orderBy('weight')->get() as $gateway) {
            $tree[] = ['key' => $gateway->id, 'title' => $gateway->name];
        }

        return $tree;
    }

    public function getSettings()
    {
        return (new \App\Src\DataTransformer\ArrayToJson())->reverseTransform($this->get('settings'));
    }
    
    public function setSettings(array $settings = [])
    {
        $this->settings = (new \App\Src\DataTransformer\ArrayToJson())->transform($settings);

        return $this;
    }

    public function getGatewayObject()
    {
        if (false === ($this->gateway instanceof \App\Src\Gateway\BaseGateway)) {
            $class = '\\App\\Gateways\\' . ucfirst(strtolower($this->slug));

            if (class_exists($class)) {
                $this->gateway = new $class($this);
            }
        }

        return $this->gateway;
    }

    public function delete($id = null)
    {
        return null;
    }

}
