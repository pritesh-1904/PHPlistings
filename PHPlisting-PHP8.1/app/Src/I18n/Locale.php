<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\I18n;

class Locale
{

    protected $locale;
    protected $languages;
    protected $loader;

    public function __construct(\App\Src\Http\Request $request)
    {
        $class = '\\App\\Src\\I18n\\StorageHandler\\' . ucfirst(strtolower(config()->app->locale_storage));

        if (class_exists($class) && is_subclass_of($class, '\\App\\Src\\I18n\\StorageHandler\\StorageHandlerInterface')) {
            $this->loader = new $class();
        } else {
            throw new \Exception('I18n storage handler "' . config()->app->locale_storage . '" is not supported');
        }

        $this->languages = \App\Models\Language::where('active', 1)->orderBy('weight')->get();

        if (0 == $this->languages->count()) {
            throw new \Exception('No supported languages found');
        }

        $this->setLocale($this->getDefault());
        
        if (true === config()->app->locale_browser && false === config()->app->locale_url_default_exclude) {
            foreach($this->getAcceptedLanguages($request) as $locale) {
                if ($this->isSupported($locale)) {
                    $this->setLocale($locale);
                    break;
                }
            }
        }

        if (null !== $locale = $this->getLocaleFromPath($request->relativePath())) {
            $this->setLocale($locale);
        } else if (false !== config()->app->locale_url_default_exclude) {
            $this->setLocale($this->getDefault());
        }
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function isLocale($locale)
    {
        return $this->locale === $locale;
    }

    public function getDefault()
    {
        return $this->languages->first()->locale;
    }

    public function isDefault($locale)
    {
        return $locale == $this->languages->first()->locale;
    }

    public function getFallback()
    {
        return config()->app->locale_fallback;
    }

    public function getSupported()
    {
        return $this->getSupportedWithOptions()->pluck('locale')->all();
    }

    public function getSupportedWithOptions()
    {
        $supported = [];

        foreach ($this->languages as $language) {
            if ($this->getLoader()->localeExists($language->locale)) {
                $supported[] = $language;
            }
        }

        return collect($supported);
    }

    public function isSupported($locale)
    {
        return in_array($locale, $this->getSupported());
    }

    public function __call($method, $arguments)
    {
        if (0 === strpos($method, 'get')) {
            $option = strtolower(preg_replace('/(?<!^)([A-Z])/', '_$1', substr($method, 3)));
            return $this->getOption($option, $arguments[0] ?? $this->getLocale());
        } else {
            throw new \Exception('Method "' . $method . '" not found');
        }
    }

    private function getOption($item, $locale)
    {        
        $language = $this->languages->where('locale', $locale)->first();

        if (null !== $language) {
            return $language->get($item);
        } else {
            return null;
        }
    }

    public function formatNumber($float, $decimalPlaces = null)
    {
        return (new \App\Src\DataTransformer\LocalizedStringToFloat($this->getThousandsSeparator(), $this->getDecimalSeparator(), $decimalPlaces))->reverseTransform($float);
    }

    public function formatPrice($float)
    {
        $string = '';

        if (config()->billing->currency_sign_position == 'prepend') {
            $string .= config()->billing->currency_sign . '';
        }

        $string .= $this->formatNumber($float, 2);

        if (config()->billing->currency_sign_position == 'append') {
            $string .= ' ' . config()->billing->currency_sign;
        }

        return $string;
    }

    public function formatTime($time)
    {
        if (null === $time) {
            return;
        }

        return (new \App\Src\DataTransformer\LocalizedStringToTime($this->getTimeFormat()))->reverseTransform($time);
    }

    public function formatTimeISO8601($time) {
        return $time;
    }

    public function formatDate($date)
    {
        return (new \App\Src\DataTransformer\LocalizedStringToDate($this->getDateFormat()))->reverseTransform($date);
    }

    public function formatDateISO8601($date) {
        return $date;
    }

    public function formatDatetime($datetime, $timezone = null, $diff = false, $format = null, $plain = false)
    {
        if (null === $datetime) {
            return;
        }

        if (false === $diff) {
            if (null !== $format) {
                if (null !== $timezone) {
                    return (new \App\Src\DataTransformer\LocalizedStringToDatetimeTZ($format, $timezone))->reverseTransform($datetime);
                }

                return (new \App\Src\DataTransformer\LocalizedStringToDatetime($format))->reverseTransform($datetime);
            }            
            
            if (null !== $timezone) {    
                return (new \App\Src\DataTransformer\LocalizedStringToDatetimeTZ($this->getDateFormat() . ' ' . $this->getTimeFormat(), $timezone))->reverseTransform($datetime);
            } else {
                return (new \App\Src\DataTransformer\LocalizedStringToDatetime($this->getDateFormat() . ' ' . $this->getTimeFormat()))->reverseTransform($datetime);
            }
        } else {
            $then = new \DateTime($datetime, new \DateTimeZone($timezone ?? '+0000'));
            $now = new \DateTime('now', new \DateTimeZone('+0000'));

            $interval = [
                'years' => $now->diff($then)->format('%y'),
                'months' => $now->diff($then)->format('%m'),
                'days' => $now->diff($then)->format('%d'),
                'hours' => $now->diff($then)->format('%h'),
                'minutes' => $now->diff($then)->format('%i'),
                'seconds' => $now->diff($then)->format('%s'),
            ];

            $difference = [];

            foreach ($interval as $name => $value) {
                if (count($difference) == 1) {
                    break;
                }

                if ($value > 0) {
                    $difference[] = __('datetime.interval.' . $name, $interval, $value);
                }
            }

            if (count($difference) == 0) {
                return __('datetime.interval.now');
            }

            if (false !== $plain) {
                return __('datetime.interval', ['interval' => implode(' ', $difference)]);
            }

            return __(($then < $now ? 'datetime.interval.past' : 'datetime.interval.future'), ['interval' => implode(' ', $difference)]);
        }
    }

    public function formatDatetimeISO8601($datetime, $timezone = null) {
        return (new \DateTime($datetime, (null !== $timezone ? new \DateTimeZone($timezone) : null)))
            ->format('Y-m-d\TH:i:sP');
    }

    public function formatDatetimeRFC822($datetime, $timezone = null) {
        return (new \DateTime($datetime, (null !== $timezone ? new \DateTimeZone($timezone) : null)))
            ->format('D, d M Y H:i:s O');
    }

    public function formatDatetimeDiff($datetime, $timezone = null)
    {
        return $this->formatDatetime($datetime, $timezone, true);
    }

    public function formatDatetimeDiffPlain($datetime, $timezone = null)
    {
        return $this->formatDatetime($datetime, $timezone, true, null, true);
    }

    public function formatFilesize($bytes)
    {
        $i = floor(log($bytes) / log(1024));

        $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

        return sprintf('%.02F', $bytes / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
    }

    public function getDaysOfWeek()
    {
        $array = [];

        for ($i = 1; $i <= 7; $i++) {
            $array[$i] = __('hour.dow.' . $i);
        }

        return $array;
    }

    public function isLocalizedPath($path)
    {
        return $path !== $this->getNonLocalizedPath($path);
    }

    public function getLocalizedPath($path, $locale = null)
    {
        if (null !== $locale) {
            if (!$this->isSupported($locale)) {
                throw new \Exception('Locale not supported');
            }
        }

        return '/' . ($locale ?? $this->getLocale()) . $this->getNonLocalizedPath($path);
    }

    public function getNonLocalizedPath($path)
    {
        $segments = preg_split('/\//', trim($path, '/'), 0, PREG_SPLIT_NO_EMPTY);

        $locale = array_shift($segments);

        if ($this->isSupported($locale)) {
            return '/' . implode('/', $segments);
        }

        return '/' . trim($path, '/');
    }

    public function getLocaleFromPath($path)
    {
        $segments = preg_split('/\//', trim($path, '/'), 0, PREG_SPLIT_NO_EMPTY);
        $locale = array_shift($segments);

        if ($this->isSupported($locale)) {
            return $locale;
        }

        return null;
    }

    public function getLoader()
    {
        return $this->loader;
    }

    protected function getAcceptedLanguages(\App\Src\Http\Request $request)
    {
        $accepted = explode(',', $request->server['HTTP_ACCEPT_LANGUAGE'] ?? '');
        $preferred = [];

        foreach ($accepted as $language) {
            if ($language != '') {
                $segments = explode(';', $language);                
                $code = $this->sanitizeLocaleName($segments[0]);
                $weight = (isset($segments[1]) ? str_replace('q=', '', $segments[1]) : 1);
                $preferred[$code] = $weight;

                $segments = explode('-', $code);
                if (isset($segments[1])) {
                    $preferred[$this->sanitizeLocaleName($segments[0])] = $weight;
                }
            }
        }

        arsort($preferred, SORT_NUMERIC);

        return array_keys($preferred);
    }

    private function sanitizeLocaleName($locale)
    {
        $segments = array_map('trim', explode('-', preg_replace('[^A-Za-z-]', '', str_replace('_', '-', $locale))));

        if (count($segments) == 1) {
            return $this->sanitizeLanguageTag($segments[0]);
        } else if (count($segments) > 1) {
            return $this->sanitizeLanguageTag($segments[0]) . '-' . $this->sanitizeRegionName($segments[1]);
        }
    }

    private function sanitizeLanguageTag($tag)
    {
        $tag = strtolower($tag);

        if (in_array($tag, ['x', 'i'])) {
            return $tag;
        } else {
            return strtolower(substr($tag, 0, 3));
        }
    }

    private function sanitizeRegionName($region)
    {
        return strtoupper(substr($tag, 0, 2));
    }

}
