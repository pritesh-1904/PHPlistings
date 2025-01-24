<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\DataTransformer;

class LocalizedStringToDate
    implements DataTransformerInterface
{

    protected $format;

    public function __construct($format = 'Y/m/d')
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

        $date = \DateTime::createFromFormat($this->format, $value);

        if (false === $date) {
            throw new FailedTransformationException('String can not be transformed to date');
        }

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

        $date = \DateTime::createFromFormat('Y-m-d', $value);

        if (false === $date) {
            throw new FailedTransformationException('Date can not be transformed to localized date');
        }

        return $date->format($this->format);
    }

}
