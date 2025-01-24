<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Password
    extends Type
{

    public $defaultConstraints = 'password:8';

    public function getOutputableValue($schema = false)
    {
        return null;
    }

    public function exportValue()
    {
        return '';
    }

    public function render()
    {
        return view('form/field/password', $this);
    }

}
