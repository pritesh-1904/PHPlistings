<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class UserGroup
    extends \App\Src\Orm\Model
{

    protected $table = 'usergroups';
    protected $fillable = [
        'name',
    ];
    protected $sortable = [
        'id' => ['id'],
        'name' => ['name'],
    ];
    protected $translatable = [
        'name',
    ];

    public function roles()
    {
        return $this->belongsToMany('App\Models\UserRole');
    }

    public function accounts()
    {
        return $this->hasMany('App\Models\Account');
    }

    public function delete($id = null)
    {
        $this->roles()->detach();
        
        return parent::delete($id);
    }

}
