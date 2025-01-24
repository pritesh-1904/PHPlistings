<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class CreditcardValidator
    implements ValidatorInterface
{

    public function validate($value, $context = null)
    {
        if (null !== $value && '' !== $value) {
            $response = \Freelancehunt\Validators\CreditCard::validCreditCard($value);

            if (false === isset($response['valid']) || '1' != $response['valid']) {
                throw new ValidatorException(__('form.validation.creditcard'));
            }
        }
    }

}
