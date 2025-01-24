<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Dbal;

class Raw
{

    public $query;
    public $parameters;

    public function __construct($query, $parameters = [])
    {
        $this->query = $query;
        $this->parameters = $parameters;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function __toString()
    {
        return $this->query;
    }

}
