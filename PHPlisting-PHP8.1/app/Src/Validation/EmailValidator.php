<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class EmailValidator
    implements ValidatorInterface
{

    public function validate($value, $context = null)
    {
        if (null !== $value && '' !== $value && false === filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new ValidatorException(__('form.validation.email'));
        }
    }

}
