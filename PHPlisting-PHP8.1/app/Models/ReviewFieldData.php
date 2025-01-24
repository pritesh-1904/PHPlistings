<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class ReviewFieldData
    extends \App\Src\Orm\Model
{

    protected $table = 'reviewfielddata';
    protected $incrementing = false;
    protected $fillable = [
        'value',
    ];

    public function review()
    {
        return $this->belongsTo('App\Models\Review');
    }

}
