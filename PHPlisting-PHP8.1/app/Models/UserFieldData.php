<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class UserFieldData
    extends \App\Src\Orm\Model
{

    protected $table = 'userfielddata';
    protected $incrementing = false;
    protected $fillable = [
        'value',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function field()
    {
        return $this->belongsTo('App\Models\Field');
    }

}
