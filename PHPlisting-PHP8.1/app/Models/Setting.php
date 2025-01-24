<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Setting
    extends \App\Src\Orm\Model
{

    protected $table = 'settings';

    public function group()
    {
        return $this->belongsTo('App\Models\SettingGroup');
    }

}
