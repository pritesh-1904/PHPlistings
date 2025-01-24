<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Validation;

class BacklinkValidator
    implements ValidatorInterface
{

    public function validate($value, $context = null)
    {
        if (null !== $value && '' !== $value) {
            if (false === filter_var($value, \FILTER_VALIDATE_URL) || false === array_key_exists('scheme', parse_url($value))) {
                throw new ValidatorException(__('form.validation.backlink.invalid'));
            }

            $url = ('' != config()->other->get('backlinkchecker_url', '') ? config()->other->get('backlinkchecker_url') : config()->app->url);

            if (parse_url($value, \PHP_URL_HOST) == parse_url($url, \PHP_URL_HOST)) {
                throw new ValidatorException(__('form.validation.backlink.invalid'));
            }

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

            $responseText = curl_exec($ch);
            $responseCode = curl_getinfo($ch, \CURLINFO_RESPONSE_CODE);
            curl_close($ch);

            if ('200' != $responseCode || false === $responseText) {
                throw new ValidatorException(__('form.validation.backlink.unreachable', ['code' => (int) $responseCode]));
            }

            $dom = new \DOMDocument();

            if (false === @$dom->loadHTML($responseText)) {
                throw new ValidatorException(__('form.validation.backlink.parse'));
            }

            $xpath = new \DOMXPath($dom);

            if (false === $tags = $xpath->evaluate("/html/body//a")) {
                throw new ValidatorException(__('form.validation.backlink.parse'));
            }

            $valid = false;
            
            for ($i = 0; $i < $tags->length; $i++) {
                $tag = $tags->item($i);

                $href = $tag->getAttribute('href');
                $rel = $tag->getAttribute('rel');
                                    
                if (trim($href, '/ ') == $url) {
                    if (null !== config()->other->backlinkchecker_follow_only && false !== stristr($rel, 'nofollow')) {
                        throw new ValidatorException(__('form.validation.backlink.follow_only'));
                    }

                    $valid = true;

                    break;
                }
            }

            if (false === $valid) {
                throw new ValidatorException(__('form.validation.backlink.not_found'));
            }
        }
    }

}
