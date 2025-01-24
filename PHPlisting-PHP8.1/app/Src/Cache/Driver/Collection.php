<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Cache\Driver;

class Collection
    extends \App\Src\Support\Collection
    implements \App\Src\Cache\Driver\DriverInterface
{

    public function get($offset, $default = null)
    {
        if ($this->offsetExists($offset)) {
            if (parent::get($offset) instanceof \Closure) {
                parent::put($offset, parent::get($offset)());
            }

            return parent::get($offset);
        }

        return $default;
    }

    public function put($offset, $value, $seconds = 0)
    {
        parent::put($offset, $value);
    }

    public function has($offset)
    {
        return $this->offsetExists($offset);
    }

    public function forget($offset)
    {
        $this->offsetUnset($offset);

        return true;
    }

}
