<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Url
    extends Type
{

    public $defaultConstraints = 'url';

    public function getOutputableValue($schema = false)
    {
        return $this->addSchema(
            view('form/field/outputable/url', ['value' => $this->getValue()]),
            $this->getItemProperty(),
            null,
            $schema
        );
    }

    public function importValue($value, $fieldModel, $locale)
    {
        if ('' != $value && (false === filter_var($value, FILTER_VALIDATE_URL) || false === array_key_exists('scheme', parse_url($value)))) {
            return false;
        }

        return $this->sanitize($value);
    }

    public function render()
    {
        return view('form/field/text', $this);
    }

}
