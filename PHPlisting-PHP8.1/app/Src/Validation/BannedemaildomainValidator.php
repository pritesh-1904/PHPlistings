<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class BannedemaildomainValidator
    implements ValidatorInterface
{

    public function validate($value, $context = null)
    {
        if (null !== $value && '' !== $value) {
            $fragments = explode('@', $value);

            if (false !== isset($fragments[1]) && '' != $fragments[1]) {
                $domains = array_filter(array_map('trim', (explode("\n", config()->other->banned_email_domains))));

                foreach ($domains as $domain) {
                    if (strtolower($fragments[1]) == strtolower($domain)) {
                        throw new ValidatorException(__('form.validation.banned_email_domain', ['domain' => strtolower($domain)]));
                    }

                    if (0 === strpos($domain, '.')) {
                        $length = strlen($domain);

                        if (substr(strtolower($fragments[1]), -$length) === strtolower($domain)) {
                            throw new ValidatorException(__('form.validation.banned_email_domain', ['domain' => strtolower($domain)]));
                        }
                    }
                }
            }
        }
    }

}
