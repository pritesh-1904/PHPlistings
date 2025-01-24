<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Button
    extends Type
{

    public function isAction()
    {
        return true;
    }

    public function setValue($value)
    {
        return;
    }

    public function getWeight()
    {
        return ($this->weight ?? 1000);
    }

    public function getLabel()
    {
        return;
    }
    
    public function render()
    {
        return view('form/field/button', $this);
    }

}
