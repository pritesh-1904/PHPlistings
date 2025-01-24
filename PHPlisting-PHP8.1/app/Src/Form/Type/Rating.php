<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Rating
    extends Type
{

    public $defaultConstraints = 'number|min:0|max:5';

    public function getOutputableValue($schema = false)
    {
        return view('form/field/outputable/rating', ['value' => $this->getValue()]);
    }

    public function importValue($value, $fieldModel, $locale)
    {
        if ('' != $value && (false === is_numeric($value) || $value < 0 || $value > 5)) {
            return false;
        }

        return $value;
    }

    public function render()
    {
        return view('form/field/rating', $this);
    }

}
