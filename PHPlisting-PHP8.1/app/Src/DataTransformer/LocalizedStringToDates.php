<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\DataTransformer;

class LocalizedStringToDates
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

        $dates = [];

        foreach (explode(',', $value) as $fragment) {       
            if (false === $date = \DateTime::createFromFormat($this->format, trim($fragment))) {
                throw new FailedTransformationException('String fragment can not be transformed into date');
            }

            $dates[] = $date->format('Y-m-d');
        }

        return implode(', ', $dates);
    }

    public function reverseTransform($value)
    {
        if ('' === $value || null === $value) {
            return $value;
        }

        if (!is_string($value)) {
            throw new FailedTransformationException('Expected string');
        }

        $dates = [];

        foreach (explode(',', $value) as $fragment) {       
            if (false === $date = \DateTime::createFromFormat('Y-m-d', trim($fragment))) {
                throw new FailedTransformationException('String fragment can not be transformed into date');
            }

            $dates[] = $date->format($this->format);
        }

        return implode(', ', $dates);
    }

}
