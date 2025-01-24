<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Dbal\Driver\Mysql\Expression;

class Notin
    extends \App\Src\Dbal\Driver\Mysql\Expression\AbstractExpression
    implements \App\Src\Dbal\Driver\ExpressionInterface
{

    public function getParameterValue()
    {
        if ($this->getValue() instanceof \App\Src\Dbal\Query) {
            return null;
        }

        return $this->getValue();
    }

    public function __toString()
    {
        if ($this->getValue() instanceof \App\Src\Dbal\Query) {
            return $this->getColumn() . ' NOT IN(' . $this->getValue() . ')';
        }
                
        return $this->getColumn() . ' NOT IN(' . implode(',', array_fill(0, count((array) $this->getValue()), '?')) . ')';
    }

}
