<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Dates
    extends Date
{

    public $defaultConstraints = 'dates';

    private function getTransformer()
    {
        return new \App\Src\DataTransformer\LocalizedStringToDates(locale()->getDateFormat());
    }

    public function transform($value)
    {
        return $this->getTransformer()->transform($value);
    }

    public function reverseTransform($value)
    {
        return $this->getTransformer()->reverseTransform($value);
    }

    public function getOutputableValue($schema = false, $delimiter = ', ')
    {
        $dates = array_map('trim', explode(',', $this->getValue()));

        $response = [];

        foreach ($dates as $date) {
            $response[] = $this->addSchema(
                locale()->formatDate($date),
                $this->getItemProperty(),
                locale()->formatDateISO8601($date),
                $schema
            );
        }

        return implode($delimiter, $response);
    }

    public function importValue($value, $fieldModel, $locale)
    {
        if ('' != $value) {
            foreach (array_map('trim', explode(',', $value)) as $date) {
                if (false === \DateTime::createFromFormat('Y-m-d', $date)) {
                    return false;
                }
            }
        }

        return $value;
    }

    public function render()
    {
        return view('form/field/dates', $this);
    }

}
