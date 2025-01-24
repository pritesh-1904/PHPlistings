<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Submit
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
    
    public function getOutputableValue($schema = false)
    {
        return null;
    }

    public function exportValue()
    {
        return '';
    }

    public function importValue($value, $fieldModel, $locale)
    {
        return '';
    }

    public function render()
    {
        return view('form/field/submit', $this);
    }

}
