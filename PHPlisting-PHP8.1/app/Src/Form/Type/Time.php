<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Time
    extends Type
{

    public $defaultConstraints = 'time';

    private function getTransformer()
    {
        return new \App\Src\DataTransformer\LocalizedStringToTime(locale()->getTimeFormat());
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
            locale()->formatTime($this->getValue()),
            $this->getItemProperty(),
            locale()->formatTimeISO8601($this->getValue()),
            $schema
        );
    }

    public function importValue($value, $fieldModel, $locale)
    {
        if ('' != $value && false === \DateTime::createFromFormat('H:i:s', $value)) {
            return false;
        }

        return $value;
    }

    public function render()
    {
        return view('form/field/time', $this);
    }

}
