<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class UrlValidator
    implements ValidatorInterface
{

    public $type;

    public function __construct($type = 'basic')
    {
        $this->type = $type;
    }

    public function validate($value, $context = null)
    {
        if (null !== $value && '' !== $value) {
            if (false === filter_var($value, \FILTER_VALIDATE_URL) || false === array_key_exists('scheme', parse_url($value))) {
                throw new ValidatorException(__('form.validation.url.invalid'));
            }

            if ('advanced' == $this->type) {
                $ch = curl_init(d($value));
                curl_setopt($ch, \CURLOPT_HEADER, true);
                curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, \CURLOPT_FOLLOWLOCATION, true);

                curl_setopt($ch, \CURLOPT_TIMEOUT, 10);

                curl_setopt($ch, \CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:104.0) Gecko/20100101 Firefox/104.0');
                curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8']);
                curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Accept-Language: en']);
                curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Accept-Encoding: gzip, deflate, br']);
                curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Connection: keep-alive']);
                curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Upgrade-Insecure-Requests: 1']);

                curl_setopt($ch, \CURLOPT_REFERER, $_SERVER['REQUEST_URI']);

                curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Sec-Fetch-Dest: document']);
                curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Sec-Fetch-Mode: navigate']);
                curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Sec-Fetch-Site: cross-site']);
                curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Pragma: no-cache']);
                curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Pragma-Control: no-cache']);

                curl_setopt($ch, \CURLOPT_MAXREDIRS, 20);
                curl_setopt($ch, \CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, \CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, \CURLOPT_AUTOREFERER, true);
                curl_setopt($ch, \CURLOPT_SSLVERSION, 6);

                $output = curl_exec($ch);
                $responseCode = curl_getinfo($ch, \CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($responseCode > 0 && false === in_array($responseCode, ['404', '500'])) {
                    throw new ValidatorException(__('form.validation.url.unreachable'));
                }
            }
        }
    }

}
