<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Date
    extends \App\Src\Orm\Model
{

    protected $table = 'dates';
    protected $fillable = [
        'event_date',
    ];

    public function listing()
    {
        return $this->belongsTo('App\Models\Listing');
    }

}
