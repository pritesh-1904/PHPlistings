<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Cache;

class Repository
{

    private $drivers;

    public function __call($method, $parameters) {
        if (in_array(strtolower($method), config()->cache->drivers_supported->all())) {
            return $this->storage($method);
        } else {
            return $this->storage(config()->cache->driver_default)->$method(...$parameters);
        }        
    }

    public function storage($driver)
    {
        if (isset($this->drivers[$driver])) {
            return $this->drivers[$driver];
        }
        
        $class = '\\App\\Src\\Cache\\Driver\\' . ucfirst(strtolower($driver));
        if (class_exists($class) && is_subclass_of($class, '\\App\\Src\\Cache\\Driver\\DriverInterface')) {
            return $this->drivers[$driver] = new $class();
        } else {
            throw new \Exception('Cache driver "' . $driver . '" is not supported');
        }
    }

}
