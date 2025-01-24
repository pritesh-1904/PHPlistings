<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class TransrequiredValidator
    implements ValidatorInterface
{

    public function validate($value, $context = null)
    {
        $locale = locale()->getDefault();

        if (!isset($value[$locale]) || null === $value[$locale] || $value[$locale] === '') {
            throw new ValidatorException(__('form.validation.required'));
        }
    }

}
