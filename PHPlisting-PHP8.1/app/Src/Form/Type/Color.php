<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Color
    extends Type
{

    public $defaultConstraints = 'color';

    public function getOutputableValue($schema = false)
    {
        return view('form/field/outputable/color', ['value' => $this->getValue()]);
    }

    public function importValue($value, $fieldModel, $locale)
    {
        if ('' != $value && (strlen($value) !== 7 || false === ctype_xdigit(substr($value, 1, 6)))) {
            return false;
        }

        return $this->sanitize($value);
    }

    public function render()
    {
        return view('form/field/color', $this);
    }

}
