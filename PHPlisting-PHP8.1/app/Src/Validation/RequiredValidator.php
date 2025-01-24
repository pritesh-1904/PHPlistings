<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class RequiredValidator
    implements ValidatorInterface
{

    public function validate($value, $context = null)
    {
        if (null === $value || (is_array($value) && count($value) === 0) || (is_string($value) && $value === '')) {
            throw new ValidatorException(__('form.validation.required'));
        }
    }

}
