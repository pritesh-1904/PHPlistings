<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Mvc;

abstract class BaseController
{

    public function action($name, array $arguments = [])
    {        
        $action = 'action' . $name;

        if (method_exists($this, $action)) {
            return $this->{$action}($arguments);
        } else {
            throw new \Exception('Action "' . $action . '" not found.');
        }
    }

}
