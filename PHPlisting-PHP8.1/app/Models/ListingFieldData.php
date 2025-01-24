<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class ListingFieldData
    extends \App\Src\Orm\Model
{

    protected $table = 'listingfielddata';
    protected $incrementing = false;
    protected $fillable = [
        'value',
    ];

    public function listing()
    {
        return $this->belongsTo('App\Models\Listing');
    }

}
