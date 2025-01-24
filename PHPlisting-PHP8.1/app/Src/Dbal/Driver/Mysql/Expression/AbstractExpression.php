<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Dbal\Driver\Mysql\Expression;

abstract class AbstractExpression
{

    protected $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function getColumn()
    {
        return $this->parameters[0];
    }

    public function getAlias()
    {
        return null;
    }

    public function getValue()
    {
        return $this->parameters[1];
    }

    public function getParameterValue()
    {
        return $this->getValue();
    }

}
