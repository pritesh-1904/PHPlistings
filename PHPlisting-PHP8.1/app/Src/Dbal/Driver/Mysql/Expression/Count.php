<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Dbal\Driver\Mysql\Expression;

class Count
    extends \App\Src\Dbal\Driver\Mysql\Expression\AbstractExpression
    implements \App\Src\Dbal\Driver\ExpressionInterface
{

    public function getColumn()
    {
        return $this->parameters[0] ?? '*';
    }

    public function getAlias()
    {
        return $this->parameters[1] ?? null;
    }

    public function getValue()
    {
        return null;
    }
    
    public function getParameterValue()
    {
        return null;
    }

    public function __toString()
    {
        return 'COUNT(' . $this->getColumn() . ')' . (null !== $this->getAlias() ? ' AS ' . $this->getAlias() : '');
    }

}
