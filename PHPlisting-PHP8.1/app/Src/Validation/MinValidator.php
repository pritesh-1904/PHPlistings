<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class MinValidator
    implements ValidatorInterface
{

    public $value;

    public function __construct($value = null)
    {
        if (null === $value || '' == $value) {
            $this->value = 1;
        } else {        
            $this->value = $value;
        }
    }

    public function validate($value, $context = null)
    {
        if (null !== $value && '' !== $value && (float) $value < $this->value) {
            throw new ValidatorException(__('form.validation.min', ['value' => $this->value]));
        }
    }

}
