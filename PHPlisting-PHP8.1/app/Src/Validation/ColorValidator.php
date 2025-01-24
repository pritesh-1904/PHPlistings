<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class ColorValidator
    implements ValidatorInterface
{

    public function validate($value, $context = null)
    {
        if (null !== $value && '' !== $value) {
            $pattern = '/(?:#|0x)(?:[a-f0-9]{3}|[a-f0-9]{6})\b|(?:rgb|hsl)a?\([^\)]*\)/i';

            if (false === preg_match($pattern, $value) || 1 !== preg_match($pattern, $value)) {
                throw new ValidatorException(__('form.validation.color'));
            }
        }
    }

}
