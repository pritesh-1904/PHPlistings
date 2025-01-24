<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Reminder
    extends \App\Src\Orm\Model
{

    protected $table = 'reminders';

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function performInsert()
    {
        $this->verification_code = bin2hex(random_bytes(16));

        return parent::performInsert();
    }

}
