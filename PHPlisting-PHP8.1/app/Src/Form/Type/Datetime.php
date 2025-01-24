<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Datetime
    extends Type
{

    public $defaultConstraints = 'datetime';

    private function getTransformer()
    {
        return new \App\Src\DataTransformer\LocalizedStringToDatetime(locale()->getDateFormat() . ' ' . locale()->getTimeFormat());
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
            locale()->formatDatetime($this->getValue()), // (null !== $this->getForm() ? $this->getForm()->getTimezone() : config()->general->timezone)),
            $this->getItemProperty(),
            locale()->formatDatetimeISO8601($this->getValue()),
            $schema
        );
    }

    public function importValue($value, $fieldModel, $locale)
    {
        if ('' != $value && false === \DateTime::createFromFormat('Y-m-d H:i:s', $value)) {
            return false;
        }

        return $value;
    }

    public function render()
    {
        return view('form/field/datetime', $this);
    }

}
