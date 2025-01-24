<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Custom
    extends Type
{

    public function getValue()
    {        
    }

    public function setValue($value)
    {
    }

    public function setRawValue($value)
    {
    }

    public function render()
    {
        return view('form/field/custom', $this);
    }

}
