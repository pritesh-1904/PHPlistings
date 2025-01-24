<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class SocialProfileType
    extends \App\Src\Orm\Model
{

    protected $table = 'socialprofiletypes';
    protected $sortable = [
        'id' => ['id'],
        'name' => ['name'],
    ];

    public function getDropdownTree()
    {
        return $this->getQuery()->get()->pluck('name', 'id')->all();
    }

}
