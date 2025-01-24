<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Cronjob
    extends \App\Src\Orm\Model
{

    protected $table = 'cronjobs';

    public function getScheduledJobs()
    {
        return $this->newQuery()
            ->whereNull('last_run_datetime')
            ->orWhere(db()->raw('NOW() >= DATE_ADD(last_run_datetime, INTERVAL exec_interval MINUTE)'))
            ->get();
    }

}
