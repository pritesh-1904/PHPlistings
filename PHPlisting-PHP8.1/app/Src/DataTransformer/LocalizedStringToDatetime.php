<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\DataTransformer;

class LocalizedStringToDatetime
    implements DataTransformerInterface
{

    protected $format;

    public function __construct($format = 'Y/m/d H:i:s')
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

        $datetime = \DateTime::createFromFormat($this->format, $value);

        if (false === $datetime) {
            throw new FailedTransformationException('String can not be transformed to datetime');
        }

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

        $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $value);

        if (false === $datetime) {
            throw new FailedTransformationException('Date can not be transformed to localized date');
        }

        return $datetime->format($this->format);
    }

}
