<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class MessageFieldData
    extends \App\Src\Orm\Model
{

    protected $table = 'messagefielddata';
    protected $incrementing = false;
    protected $fillable = [
        'value',
    ];

    public function message()
    {
        return $this->belongsTo('App\Models\Message');
    }

}
