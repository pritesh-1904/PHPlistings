<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\DataTransformer;

class LocalizedStringToTimeTZ
    implements DataTransformerInterface
{

    protected $timeFormat;
    protected $timezone;

    public function __construct($timeFormat = 'H:i:s', $timezone = null)
    {
        $this->timeFormat = $timeFormat;
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

        $time = \DateTime::createFromFormat($this->timeFormat, $value, new \DateTimeZone($timezone));

        if (false === $time) {
            throw new FailedTransformationException('String can not be transformed to time');
        }

        date_timezone_set($time, new \DateTimeZone('+0000'));

        return $time->format('H:i:s');
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
            $timezone = (false !== auth()->check()) ? auth()->user()->timezone : config()->app->timezone;
        }

        $time = \DateTime::createFromFormat('H:i:s', $value, new \DateTimeZone('+0000'));

        if (false === $time) {
            throw new FailedTransformationException('Time can not be transformed to localized time');
        }

        date_timezone_set($time, new \DateTimeZone($timezone));

        return $time->format($this->timeFormat);
    }

}
