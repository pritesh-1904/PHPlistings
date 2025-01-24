<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Form\Type;

class Checkbox
    extends Type
{

    private function getTransformer()
    {
        return new \App\Src\DataTransformer\ArrayToSet();
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
        $value = $this->reverseTransform($this->getValue());

        $response = [];

        foreach ($this->getOptions() as $key => $option) {
            if (in_array($key, $value)) {
                $response[] = $this->addSchema(e($option), null, $key, $schema);
            }
        }

        return $this->addSchema(
            implode($delimiter, $response),
            $this->getItemProperty(),
            null,
            $schema
        );
    }

    public function exportValue()
    {
        return $this->getOutputableValue(false, ',');
    }

    public function importValue($value, $fieldModel, $locale)
    {
        $value = trim($value);

        if ('' == $value) {
            return $value;
        }

        $value = explode(',', $value);

        $keys = [];

        foreach ($value as $option) {
            $option = trim($this->sanitize($option));
            
            if ('' == $option) {
                continue;
            }

            if (null !== $temp = $fieldModel->options()->where('value', 'like', '%"' . $locale . '":"' . $option . '"%')->first()) {
                $keys[] = $temp->name;
            } else {
                if (false !== ($fieldModel instanceof \App\Models\ListingField)) {
                    $temp = new \App\Models\ListingFieldOption();
                } else {
                    $temp = new \App\Models\FieldOption();
                }

                $temp->customizable = 1;
                $temp->name = str_replace('-', '', slugify($option));
                $temp->setTranslation('value', $option, config()->app->locale_fallback);

                if (config()->app->locale_fallback != $locale) {
                    $temp->setTranslation('value', $option, $locale);
                }

                $fieldModel->options()->save($temp);

                $keys[] = $temp->name;
            }
        }
        
        return implode(',', array_unique($keys));
    }

    public function render()
    {
        return view('form/field/checkbox', $this);
    }

}
