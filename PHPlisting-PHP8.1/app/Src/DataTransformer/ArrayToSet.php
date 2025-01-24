<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\DataTransformer;

class ArrayToSet
    implements DataTransformerInterface
{

    protected $separator;

    public function __construct($separator = ',')
    {
        $this->separator = $separator;

        if (!is_string($separator) || $separator === '') {
            throw new FailedTransformationException('Separator must be a non-empty string');
        }
    }

    public function transform($value)
    {
        if ('' === $value || null === $value) {
            return $value;
        }

        if (!is_array($value)) {
            throw new FailedTransformationException('Expected array');
        }

        return implode($this->separator, $value);
    }

    public function reverseTransform($value)
    {
        if ('' === $value || null === $value) {
            return [];
        }

        if (!is_string($value)) {
            throw new FailedTransformationException('Expected string');
        }

        return explode($this->separator, $value);
    }

}
