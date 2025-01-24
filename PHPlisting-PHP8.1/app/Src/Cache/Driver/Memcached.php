<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Cache\Driver;

class Memcached
    implements \App\Src\Cache\Driver\DriverInterface
{

    private $connection;

    public function __construct()
    {
        $connection = new \Memcached();

        $exists = false;

        foreach ($connection->getServerList() as $server) {
            if ($server['host'] == config()->cache->memcached->host && $server['port'] == config()->cache->memcached->port) {
                $exists = true;
            }            
        }

        if (false === $exists) {
            $connection->addServer(config()->cache->memcached->host, config()->cache->memcached->port);
        }

        if (false === $connection) {
            throw new \Exception('Memcached host is unreachable');
        }

        $this->connection = $connection;
    }

    public function get($offset, $default = null)
    {
        if (false !== $item = $this->connection->get($offset)) {
            return $item;
        }
                    
        return $default;
    }

    public function put($offset, $value, $seconds = 0)
    {        
        if ($seconds == 0) {
            return true;
        }

        return $this->connection->set($offset, $value, $seconds);
    }

    public function has($offset)
    {
        if (false !== $item = $this->connection->get($offset)) {
            return true;
        }

        return false;
    }

    public function forget($offset)
    {
        $this->connection->delete($offset);
    }

}
