<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\DataTransformer;

class ArrayToJson
    implements DataTransformerInterface
{

    public function transform($value)
    {
        if (!is_array($value)) {
            throw new FailedTransformationException('Expected array');
        }

        return json_encode($value, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
    }

    public function reverseTransform($value)
    {
        if ('' === $value || null === $value) {
            return [];
        }

        $return = @json_decode($value, true);

        if (json_last_error() !== \JSON_ERROR_NONE) {
           throw new FailedTransformationException('Expected JSON');
        }

        return $return;
    }

}
