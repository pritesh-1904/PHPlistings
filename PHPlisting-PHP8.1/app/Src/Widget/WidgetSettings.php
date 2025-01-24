<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Widget;

class WidgetSettings
    extends \App\Src\Support\Collection
{

    protected $translatable = [];

    public function __construct(array $settings)
    {
        $this->collect($settings);
    }

    public function offsetGet($offset, $default = null)
    {
        if ($this->isTranslatable($offset)) {
            return $this->getTranslation($offset, locale()->getLocale());
        } else {
            return parent::get($offset, $default);
        }
    }

    public function getTranslation($attribute, $locale = null, $default = null)
    {
        if (null === $locale) {
            $locale = locale()->getDefault();
        }
        
        $translations = $this->getTranslations($attribute);

        if (isset($translations[$locale]) && '' !== $translations[$locale]) {
            return $translations[$locale];
        } else if (isset($translations[locale()->getDefault()]) && '' !== $translations[locale()->getDefault()]) {
            return $translations[locale()->getDefault()];
        } else {
            return $translations[locale()->getFallback()] ?? $default;
        }
    }

    public function isTranslatable($attribute)
    {
        return in_array($attribute, $this->translatable);
    }

    public function setTranslatable($attribute)
    {
        if (is_array($attribute)) {
            $this->translatable = array_merge($this->translatable, $attribute);
        } else {
            $this->translatable[] = $attribute;
        }

        return $this;
    }

    public function getTranslations($attribute)
    {
        return (new \App\Src\DataTransformer\ArrayToJson())->reverseTransform($this->get($attribute)) ?? [];
    }

}
