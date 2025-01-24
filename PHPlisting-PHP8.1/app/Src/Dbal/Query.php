<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Dbal;

class Query
{

    public $sql;
    public $parameters;

    public function __construct($sql, $parameters = [])
    {
        $this->sql = $sql;
        $this->parameters = $parameters;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function __toString()
    {
        return (string) $this->sql;
    }

}
