<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Toggle
    extends Type
{

    public function getOptions()
    {
        return ['1' => ''];
    }

    public function getOutputableValue($schema = false)
    {
        return view('form/field/outputable/toggle', ['value' => $this->getValue()]);
    }

    public function exportValue()
    {
        return null !== $this->getValue() ? '1' : '';
    }

    public function importValue($value, $fieldModel, $locale)
    {
        return ('1' == $value) ? $value : null;
    }

    public function render()
    {
        return view('form/field/toggle', $this);
    }

}
