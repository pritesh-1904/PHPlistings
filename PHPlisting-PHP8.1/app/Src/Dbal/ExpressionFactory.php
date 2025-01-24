<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Dbal;

class ExpressionFactory
{

    public function __call($type, $parameters)
    {
        return new Expression($type, $parameters);
    }

}
