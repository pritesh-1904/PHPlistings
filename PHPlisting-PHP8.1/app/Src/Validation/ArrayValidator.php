<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class ArrayValidator
    implements ValidatorInterface
{

    public function validate($value, $context = null)
    {
        if (null !== $value && !is_array($value)) {
            throw new ValidatorException(__('form.validation.array'));
        }
    }

}
