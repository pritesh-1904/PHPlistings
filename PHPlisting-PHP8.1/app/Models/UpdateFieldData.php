<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class UpdateFieldData
    extends \App\Src\Orm\Model
{

    protected $table = 'updatefielddata';
    protected $incrementing = false;
    protected $fillable = [
        'value',
    ];

    public function update()
    {
        return $this->belongsTo('App\Models\Update');
    }

    public function field()
    {
        return $this->belongsTo('App\Models\ListingField');
    }

}
