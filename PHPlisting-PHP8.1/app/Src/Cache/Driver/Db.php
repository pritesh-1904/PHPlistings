<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Cache\Driver;

class Db
    implements \App\Src\Cache\Driver\DriverInterface
{

    public function get($offset, $default = null)
    {
        if (null !== $cache = db()->table(config()->cache->db->table)
            ->select('cdata')
            ->where('cid', $offset)
            ->where('ctimestamp', '>=', time())
            ->first())
        {
            return \unserialize($cache->cdata);
        }
            
        return $default;
    }

    public function put($offset, $value, $seconds = 0)
    {        
        if ($seconds == 0) {
            return true;
        }
        
        return db()->table(config()->cache->db->table)
            ->merge([
                'cid' => $offset,
                'cdata' => \serialize($value),
                'ctimestamp' => time() + $seconds,
            ], 'cid');
    }

    public function has($offset)
    {
        if (null !== db()->table(config()->cache->db->table)
            ->where('cid', $offset)
            ->where('ctimestamp', '>=', time())
            ->first(['cid']))
        {
            return true;
        }
            
        return false;
    }

    public function forget($offset)
    {
        return db()->table(config()->cache->db->table)
            ->where('cid', $offset)
            ->where('ctimestamp', '>=', time())
            ->delete();
    }

}
