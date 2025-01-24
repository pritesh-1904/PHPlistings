<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Claim
    extends \App\Src\Orm\Model
{

    protected $table = 'claims';
    protected $fillable = [
        'status',
        'comment',
        'pricing_id',
    ];
    protected $searchable = [
        'status' => ['status', 'eq'],
        'listing_id' => ['listing_id', 'eq'],
    ];
    protected $sortable = [
        'id' => ['id'],
        'added_datetime' => ['added_datetime'],
    ];

    public function type()
    {
        return $this->belongsTo('App\Models\Type');
    }

    public function listing()
    {
        return $this->belongsTo('App\Models\Listing');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function pricing()
    {
        return $this->belongsTo('App\Models\Pricing');
    }

}
