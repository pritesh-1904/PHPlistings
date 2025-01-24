<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class TransmaxlengthValidator
    implements ValidatorInterface
{

    public $length;

    public function __construct($length = null)
    {
        if (null === $length || '' == $length) {
            $this->length = 100;
        } else {        
            $this->length = $length;
        }
    }

    public function validate($value, $context = null)
    {
        if (null !== $this->length) {
            foreach (locale()->getSupported() as $locale) {
                if (isset($value[$locale])) {
                    $length = mb_strlen(d($value[$locale]), 'UTF-8');
                    if ($length > (int) $this->length) {
                        throw new ValidatorException(__('form.validation.transmaxlength', ['length' => $this->length, 'locale' => $locale]));
                    }
                }
            }
        }
    }

    public function getParameter()
    {
        return $this->length;
    }

}
