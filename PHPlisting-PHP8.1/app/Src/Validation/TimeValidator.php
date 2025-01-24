<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class TimeValidator
    implements ValidatorInterface
{

    public function validate($value, $context = null)
    {
        if (null !== $value && '' !== $value) {
            try {
                (new \App\Src\DataTransformer\LocalizedStringToTime(locale()->getTimeFormat()))->transform($value);
            } catch (\App\Src\DataTransformer\FailedTransformationException $e) {
                throw new ValidatorException(__('form.validation.time'));
            }
        }
    }

}
