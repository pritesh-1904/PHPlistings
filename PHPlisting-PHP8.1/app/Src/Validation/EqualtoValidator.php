<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class EqualtoValidator
    implements ValidatorInterface
{

    protected $field;

    public function __construct($field)
    {
        $this->field = $field;
    }

    public function validate($value, $context = null)
    {
        if (null !== $value && '' !== $value) {
            if (null !== $context && false !== $context->has($this->field)) {
                if ($value != $context->get($this->field)) {
                    throw new ValidatorException(__('form.validation.equalto', ['field' => $this->field]));
                }
            }
        }
    }

}
