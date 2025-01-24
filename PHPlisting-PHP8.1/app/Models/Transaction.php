<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Transaction
    extends \App\Src\Orm\Model
{

    protected $table = 'transactions';
    protected $fillable = [
        'gateway',
        'amount',
        'status',
    ];

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice');
    }

    public function gateway()
    {
        return $this->belongsTo('App\Models\Gateway');
    }

    public function performInsert()
    {
        if (null === $this->get('hash')) {
            $this->hash = bin2hex(random_bytes(16));
        }

        if (null === $this->get('added_datetime')) {
            $this->added_datetime = date('Y-m-d H:i:s');
        }

        return parent::performInsert();
    }

}
