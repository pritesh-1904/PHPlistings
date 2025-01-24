<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class BannedwordsValidator
    implements ValidatorInterface
{

    public function validate($value, $context = null)
    {
        if (null !== $value && '' !== $value) {
            $words = array_filter(array_map('trim', (explode("\n", config()->other->banned_words))));
            $matches = [];

            if (count($words) > 0 && preg_match_all("/\b(" . implode('|', $words) . ")\b/i", $value, $matches)) {
                if (count(array_unique($matches[0])) > 0) {
                    throw new ValidatorException(__('form.validation.banned', ['words' => implode(', ', array_unique($matches[0]))]));
                }
            }
        }
    }

}
