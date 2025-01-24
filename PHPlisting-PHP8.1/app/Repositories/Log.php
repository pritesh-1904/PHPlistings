<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Repositories;

class Log
{

    public function push($type, $type_id)
    {
        $array = [
            'type' => $type,
            'type_id' => $type_id,
            'ip' => request()->ip(),
            'added_datetime' => date('Y-m-d H:i:s'),
        ];

        return db()->table('logs')->insert($array);
    }

    public function getTodayRecords($type, $type_id = null)
    {
        $query = \App\Models\Log::where('type', $type);

        if (null !== $type_id) {
            $query->where('type_id', $type_id);
        }

        $query->where(db()->raw('DATE(added_datetime) = CURDATE()'));

        return $query->get();
    }

}
