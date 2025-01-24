<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Radio
    extends Type
{

    public function getOutputableValue($schema = false)
    {
        return $this->addSchema(
            e($this->getOptions()[$this->getValue()] ?? ''),
            $this->getItemProperty(),
            $this->getValue(),
            $schema
        );
    }

    public function exportValue()
    {
        return $this->getOutputableValue(false);
    }

    public function importValue($value, $fieldModel, $locale)
    {
        $value = trim($this->sanitize($value));

        if ('' == $value) {
            return '';
        }

        if (null !== $temp = $fieldModel->options()->where('value', 'like', '%"' . $locale . '":"' . $value . '"%')->first()) {
            return $temp->name;
        } else {
            if (false !== ($fieldModel instanceof \App\Models\ListingField)) {
                $temp = new \App\Models\ListingFieldOption();
            } else {
                $temp = new \App\Models\FieldOption();
            }

            $temp->customizable = 1;
            $temp->name = str_replace('-', '', slugify($value));
            $temp->setTranslation('value', $value, config()->app->locale_fallback);

            if (config()->app->locale_fallback != $locale) {
                $temp->setTranslation('value', $value, $locale);
            }

            $fieldModel->options()->save($temp);

            return $temp->name;
        }
    }

    public function render()
    {
        return view('form/field/radio', $this);
    }

}
