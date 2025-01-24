<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class PasswordValidator
    implements ValidatorInterface
{

    public $length;

    public function __construct($length = null)
    {
        $this->length = $length;
    }

    public function validate($value, $context = null)
    {
        if (null !== $value && '' !== $value) {
            if (null !== $this->length) {
                $length = mb_strlen(d($value), 'UTF-8');

                if ($length < (int) $this->length) {
                    throw new ValidatorException(__('form.validation.password.minlength', ['length' => $this->length]));
                }
            }

            if (false === config()->compat->allow_weak_passwords) {
                $pattern = '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{0,}$/';
            
                if (false === preg_match($pattern, $value) || 1 !== preg_match($pattern, $value)) {
                    throw new ValidatorException(__('form.validation.password.strength'));
                }
            }
        }
    }

}
