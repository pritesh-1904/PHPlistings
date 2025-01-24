<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class TransminlengthValidator
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
            $locale = locale()->getDefault();
            if (isset($value[$locale])) {
                $length = mb_strlen(d($value[$locale]), 'UTF-8');
                if ($length < (int) $this->length) {
                    throw new ValidatorException(__('form.validation.transminlength', ['length' => $this->length, 'locale' => $locale]));
                }
            }
        }
    }

    public function getParameter()
    {
        return $this->length;
    }

}
