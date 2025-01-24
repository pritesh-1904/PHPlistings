<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Dbal\Driver\Mysql\Expression;

class Notbetween
    extends \App\Src\Dbal\Driver\Mysql\Expression\AbstractExpression
    implements \App\Src\Dbal\Driver\ExpressionInterface
{

    public function getValue()
    {
        return $this->parameters[1];
    }

    public function __toString()
    {
        return $this->getColumn() . ' NOT BETWEEN ? AND ? ';
    }

}
