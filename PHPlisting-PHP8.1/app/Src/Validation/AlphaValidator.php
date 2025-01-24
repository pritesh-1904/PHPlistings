<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class AlphaValidator
    implements ValidatorInterface
{

    public function validate($value, $context = null)
    {
        if (null !== $value && '' !== $value && 1 !== preg_match('/^[a-zA-Z]+$/', $value)) {
            throw new ValidatorException(__('form.validation.alpha'));
        }
    }

}
