<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Http\SessionHandler;

class Db
    implements \SessionHandlerInterface
{

    public function __construct()
    {
        $this->table = config()->session->table;
    }

    public function close()
    {
        $qb = db()->table(config()->session->table);

        return $qb
            ->where(
                db()->raw('sid IN (SELECT s.sid FROM (SELECT * FROM ' . $qb->getTable() . ') s GROUP BY s.sid HAVING SUM(s.slifetime + s.stimestamp) < ?)', [time()])
            )
            ->delete();
    }

    public function destroy($session_id)
    {
        return db()->table(config()->session->table)
            ->where('sid', $session_id)
            ->delete();
    }

    public function gc($maxlifetime)
    {
        return true;
    }

    public function open($save_path, $session_name)
    {
        return true;
    }

    public function read($session_id)
    {
        $session = db()->table(config()->session->table)
            ->where('sid', $session_id)
            ->first();

        if (null !== $session) {
            if (false !== $session_data = \App\Src\Support\Crypt::decode($session->sdata, config()->security->encryption_key, config()->security->authentication_key)) {
                return $session_data;
            }
        }

        return '';
    }

    public function write($session_id, $session_data)
    {
        $lifetime = (int) ini_get('session.gc_maxlifetime');

        return db()->table(config()->session->table)
            ->merge([
                'sid' => $session_id,
                'sdata' => \App\Src\Support\Crypt::encode($session_data, config()->security->encryption_key, config()->security->authentication_key),
                'slifetime' => $lifetime,
                'stimestamp' => time(),
            ], 'sid');
    }

}
