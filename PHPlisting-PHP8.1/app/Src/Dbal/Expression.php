<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Dbal;

class Expression
{

    public $type;
    public $parameters;

    public function __construct($type, $parameters = [])
    {
        $this->type = $type;
        $this->parameters = $parameters;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

}
