<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class UserRole
    extends \App\Src\Orm\Model
{

    protected $table = 'userroles';

    public function groups()
    {
        return $this->belongsToMany('App\Models\UserGroup');
    }

    public function getTree()
    {
        $tree = [];
        foreach($this->getQuery()->get() as $role) {
            $tree[] = [
                'key' => $role->id,
                'title' => $role->name,
            ];
        }

        return $tree;
    }

    public function delete($id = null)
    {
        $this->groups()->detach();
        
        return parent::delete($id);
    }

}
