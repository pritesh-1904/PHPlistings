<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class MinlengthValidator
    implements ValidatorInterface
{

    public $length;

    public function __construct($length = null)
    {
        if (null === $length || '' == $length) {
            $this->length = 1;
        } else {        
            $this->length = $length;
        }
    }

    public function validate($value, $context = null)
    {
        if (null !== $this->length) {
            if (is_array($value)) {
                if (count($value) < (int) $this->length) {
                    throw new ValidatorException(__('form.validation.minlength.array', ['length' => $this->length]));
                }
            } elseif (is_string($value)) {
                $length = mb_strlen(str_replace("\n", '', d($value)), 'UTF-8');

                if ($length < (int) $this->length) {
                    throw new ValidatorException(__('form.validation.minlength.string', ['length' => $this->length]));
                }
            }
        }
    }

}
