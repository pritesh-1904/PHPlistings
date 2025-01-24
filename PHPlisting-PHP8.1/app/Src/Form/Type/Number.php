<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Number
    extends Type
{

    public $defaultConstraints = 'number';

    private function getTransformer()
    {
        return new \App\Src\DataTransformer\LocalizedStringToFloat(locale()->getThousandsSeparator(), locale()->getDecimalSeparator());
    }

    public function transform($value)
    {
        return $this->getTransformer()->transform($value);
    }

    public function reverseTransform($value)
    {
        return $this->getTransformer()->reverseTransform($value);
    }

    public function getOutputableValue($schema = false)
    {
        return $this->addSchema(
            locale()->formatNumber($this->getValue()),
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
        return view('form/field/text', $this);
    }

}
