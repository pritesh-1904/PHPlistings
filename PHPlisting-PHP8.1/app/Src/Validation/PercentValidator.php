<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class PercentValidator
    implements ValidatorInterface
{

    public function validate($value, $context = null)
    {
        if (null !== $value && '' !== $value) {
            try {
                $value = (new \App\Src\DataTransformer\LocalizedStringToFloat(locale()->getThousandsSeparator(), locale()->getDecimalSeparator()))->transform($value);
            } catch (\App\Src\DataTransformer\FailedTransformationException $e) {
                throw new ValidatorException(__('form.validation.percent'));
            }

            if (!is_numeric($value) || 0 > $value || 100 < $value) {
                throw new ValidatorException(__('form.validation.percent'));
            }
        }
    }

}
