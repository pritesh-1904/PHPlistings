<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\DataTransformer;

class LocalizedStringToDatetimeTZ
    implements DataTransformerInterface
{

    protected $format;
    protected $timezone;

    public function __construct($format = 'Y/m/d H:i:s', $timezone = null)
    {
        $this->format = $format;
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

        $datetime = \DateTime::createFromFormat($this->format, $value, new \DateTimeZone($timezone));

        if (false === $datetime) {
            throw new FailedTransformationException('String can not be transformed to datetime');
        }

        date_timezone_set($datetime, new \DateTimeZone('+0000'));

        return $datetime->format('Y-m-d H:i:s');
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

        $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $value, new \DateTimeZone('+0000'));

        if (false === $datetime) {
            throw new FailedTransformationException('Date can not be transformed to localized date');
        }

        date_timezone_set($datetime, new \DateTimeZone($timezone));

        return $datetime->format($this->format);
    }

}
