<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\I18n\StorageHandler;

class Files
    implements StorageHandlerInterface
{

    protected $translations = [];

    public function loadTranslation($locale, $group)
    {
        if (!$this->isLoadedTranslation($locale, $group)) {
            if ($this->translationExists($locale, $group)) {
                $this->translations[$locale][$group] = require $this->getTranslationFileName($locale, $group);
            }
        }
    }

    public function isLoadedTranslation($locale, $group)
    {
        return isset($this->translations[$locale][$group]);
    }

    public function getTranslation($item, $group, $locale, $pluralize)
    {
        $this->loadTranslation($locale, $group);

        if (isset($this->translations[$locale][$group][$item])) {
            $translations = explode('|', $this->translations[$locale][$group][$item]);

            if (count($translations) == 2) {
                list ($singular, $plural) = $translations;

                return (false === $pluralize) ? $singular : $plural;
            }

            return $this->translations[$locale][$group][$item];
        }

        return implode('.', [$locale, $group, $item]);
    }

    public function localeExists($locale)
    {
        return is_dir($this->getLocaleDirectoryName($locale));
    }

    public function translationExists($locale, $group)
    {
        return is_readable($this->getTranslationFileName($locale, $group));
    }

    private function getLocaleDirectoryName($locale)
    {
        return config()->app->locale_path . DS . $locale;
    }

    private function getTranslationFileName($locale, $group)
    {
        return $this->getLocaleDirectoryName($locale) . DS . $group . '.php';
    }

}
