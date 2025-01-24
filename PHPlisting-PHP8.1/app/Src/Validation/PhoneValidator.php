<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class PhoneValidator
    implements ValidatorInterface
{

    public function validate($value, $context = null)
    {
        if (null !== $value && '' !== $value && 1 !== preg_match('/^[0-9-+ ()\/]+$/', $value)) {
            throw new ValidatorException(__('form.validation.phone'));
        }
    }

}
