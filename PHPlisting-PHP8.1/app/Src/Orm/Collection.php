<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Orm;

class Collection
    extends \App\Src\Dbal\Collection
{

    private $limit;
    private $total;

    public function load($method, \Closure $constraints = null)
    {
        $this->first()->newQuery()->eagerLoadRelation($this, $method, $constraints);

        return $this;
    }

}
