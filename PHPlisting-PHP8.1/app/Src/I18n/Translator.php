<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\I18n;

class Translator
{

    protected $locale;

    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
    }

    public function translate($key, array $replace = [], $number = 1, $locale = null, $fallback = true)
    {        
        $segments = explode('.', $key);

        if (count($segments) == 1) {
            $group = 'default';
            $key = $segments[0];
        } else {
            $group = array_shift($segments);
            $key = implode('.', $segments);
        }            

        $pluralize = $number > 1;

        if (null !== $item = $this->fetch($key, $group, $locale ?? $this->locale->getLocale(), $pluralize)) {
            return $this->replace($item, $replace);
        } else if ($fallback && null !== $item = $this->fetch($key, $group, $this->locale->getFallback(), $pluralize)) {
            return $this->replace($item, $replace);
        } else {
            return $key;
        }
    }

    private function fetch($item, $group, $locale, $pluralize)
    {
        return $this->locale->getLoader()->getTranslation(...func_get_args());
    }

    private function replace($item, array $replace)
    {
        foreach ($replace as $key => $value) {
            $item = preg_replace('/:' . $key . '\b/u', str_replace('$', '\$', $value ?? ''), $item ?? '');
        }

        return $item;
    }

}
