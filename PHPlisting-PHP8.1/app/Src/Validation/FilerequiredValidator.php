<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class FilerequiredValidator
    implements ValidatorInterface
{

    public function validate($value, $context = null)
    {
        if (null === $value || $value === '' || \App\Models\File::where('document_id', $value)->count() < 1) {
            throw new ValidatorException(__('form.validation.required'));
        }
    }

}
