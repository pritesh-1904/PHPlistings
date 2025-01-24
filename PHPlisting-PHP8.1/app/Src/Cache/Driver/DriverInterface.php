<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Cache\Driver;

interface DriverInterface
{

    public function get($offset, $default = null);
    public function put($offset, $value, $seconds = 0);
    public function has($offset);
    public function forget($offset);

}
