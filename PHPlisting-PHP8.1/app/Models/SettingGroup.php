<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class SettingGroup
    extends \App\Src\Orm\Model
{

    protected $table = 'settinggroups';

    public function settings()
    {
        return $this->hasMany('App\Models\Setting');
    }

    public function fields()
    {
        return $this->hasMany('App\Models\SettingField');
    }

    public function getFormFields()
    {
        return $this->fields()->get();
    }

}
