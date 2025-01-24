<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Http;

class Cookie
    extends \App\Src\Support\Collection
{

    public function __construct($name, $value, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = false, $sameSite = 'lax')
    {
        $this->name = $name;
        $this->value = $value;
        $this->expire = $expire;
        $this->maxAge = ($this->expire !== 0) ? $this->expire - time() : 0;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
        $this->sameSite = in_array(strtolower($sameSite), ['strict', 'lax']) ? strtolower(ucfirst($sameSite)) : null;
    }

    public function __toString()
    {
        $string = '';

        if ('' === (string) $this->value) {
            $this->value = 'deleted';
            $this->expire = time() - 60*60*24*365;
            $this->maxAge = -1*60*60*24*365;
        }

        $string .= urlencode($this->name) . '=' . rawurlencode($this->value);
        $string .= '; Expires=' . gmdate('D, d M Y H:i:s T', $this->expire);
        $string .= '; Max-Age=' . $this->maxAge;
        $string .= '; Path=' . $this->path;

        if (null !== $this->domain) {
            $string .= '; Domain=' . $this->domain;
        }

        if (true === $this->secure) {
            $string .= '; Secure';
        }

        if (true === $this->httpOnly) {
            $string .= '; HttpOnly';
        }

        if (null !== $this->sameSite) {
            $string .= '; SameSite=' . $this->sameSite;
        }

        return $string;
    }

}
