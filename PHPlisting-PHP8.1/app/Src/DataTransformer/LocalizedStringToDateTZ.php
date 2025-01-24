<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\DataTransformer;

class LocalizedStringToDateTZ
    implements DataTransformerInterface
{

    protected $format;
    protected $time;
    protected $timezone;

    public function __construct($format = 'Y/m/d', $time = '00:00:00', $timezone = null)
    {
        $this->format = $format;
        $this->time = $time;
        $this->timezone = $timezone;
    }

    public function transform($value)
    {
        if ('' === $value || null === $value) {
            return $value;
        }
        
        if (!is_string($value)) {
            throw new FailedTransformationException('Expected string');
        }

        if (null !== $this->timezone) {
            $timezone = $this->timezone;
        } else {
            $timezone = (false !== auth()->check()) ? auth()->user()->timezone : config()->general->timezone;
        }

        $date = \DateTime::createFromFormat($this->format . ' H:i:s', $value . ' ' . $this->time, new \DateTimeZone($timezone));

        if (false === $date) {
            throw new FailedTransformationException('String can not be transformed to date');
        }

        date_timezone_set($date, new \DateTimeZone('+0000'));

        return $date->format('Y-m-d');
    }

    public function reverseTransform($value)
    {
        if ('' === $value || null === $value) {
            return $value;
        }

        if (!is_string($value)) {
            throw new FailedTransformationException('Expected string');
        }

        if (null !== $this->timezone) {
            $timezone = $this->timezone;
        } else {
            $timezone = (false !== auth()->check()) ? auth()->user()->timezone : config()->general->timezone;
        }

        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $value . ' ' . $this->time, new \DateTimeZone('+0000'));

        if (false === $date) {
            throw new FailedTransformationException('Date can not be transformed to localized date');
        }

        date_timezone_set($date, new \DateTimeZone($timezone));

        return $date->format($this->format);
    }

}
