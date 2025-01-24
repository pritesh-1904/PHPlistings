<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Dbal\Driver;

interface DriverInterface
{

    public function createHandler($identifier = 'default');
    public function buildQuery(\App\Src\Dbal\QueryBuilder $query);

}
