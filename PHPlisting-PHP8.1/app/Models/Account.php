<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Account
    extends \App\Src\Orm\Model
{

    protected $table = 'accounts';

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\UserGroup');
    }

    public function setPassword($string)
    {
        $this->password = password_hash($string, PASSWORD_BCRYPT, ['cost' => 12]);
        
        return $this;
    }

    public function verifyPassword($string)
    {
        return password_verify($string, $this->get('password'));
    }

}
