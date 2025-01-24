<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\DataTransformer;

class LocalizedStringToTime
    implements DataTransformerInterface
{

    protected $format;

    public function __construct($format = 'H:i:s')
    {
        $this->format = $format;
    }

    public function transform($value)
    {
        if ('' === $value || null === $value) {
            return $value;
        }
        
        if (!is_string($value)) {
            throw new FailedTransformationException('Expected string');
        }

        $time = \DateTime::createFromFormat($this->format, $value);

        if (false === $time) {
            throw new FailedTransformationException('String can not be transformed to time');
        }

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

        $time = \DateTime::createFromFormat('H:i:s', $value);

        if (false === $time) {
            throw new FailedTransformationException('Time can not be transformed to localized time');
        }

        return $time->format($this->format);
    }

}
