<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Price
    extends Number
{

    public $defaultConstraints = 'price';

    public function getOutputableValue($schema = false)
    {
        return $this->addSchema(
            locale()->formatPrice($this->getValue()),
            $this->getItemProperty(),
            $this->getValue(),
            $schema
        );
    }

    public function importValue($value, $fieldModel, $locale)
    {
        if ('' != $value && false === is_numeric($value)) {
            return false;
        }

        return $this->sanitize($value);
    }

    public function render()
    {
        return view('form/field/price', $this);
    }

}
