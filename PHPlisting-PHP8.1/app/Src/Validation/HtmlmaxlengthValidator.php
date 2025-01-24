<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class HtmlmaxlengthValidator
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
            if (is_array($value)) {
                if (count($value) > (int) $this->length) {
                    throw new ValidatorException(__('form.validation.maxlength.array', ['length' => $this->length]));
                }
            } else if (is_string($value)) {
                $length = mb_strlen(trim(d(strip_tags(str_replace("\n", '', d($value))))), 'UTF-8');

                if ($length > (int) $this->length) {
                    throw new ValidatorException(__('form.validation.maxlength.string', ['length' => $this->length]));
                }
            }
        }
    }

    public function getParameter()
    {
        return $this->length;
    }

}
