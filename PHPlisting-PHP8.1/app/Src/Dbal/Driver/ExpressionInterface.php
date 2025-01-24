<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Dbal\Driver;

interface ExpressionInterface
{

    public function __construct(array $parameters);
    public function getColumn();
    public function getAlias();
    public function getValue();
    public function getParameterValue();
    public function __toString();

}
