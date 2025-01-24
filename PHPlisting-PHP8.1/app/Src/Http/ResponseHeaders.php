<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Http;

class ResponseHeaders
{

    protected $headers = [];

    public function set($name, $value, $replace = true)
    {
        $this->headers[$name][] = [$value, $replace];
    }

    public function setCookie(\App\Src\Http\Cookie $cookie)
    {
        $this->headers['Set-Cookie'][] = [(string) $cookie, false];
    }

    public function has($name)
    {
        return (isset($this->headers[$name])) ? true : false;
    }

    public function get($name = null)
    {
        if (null === $name) {
            return $this->headers;
        } else {
            return ($this->has($name)) ? $this->headers[$name] : null;
        }
    }

    public function getCookies()
    {
        return $this->headers['Set-Cookie'];
    }

    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->headers[$name]);
        }
    }

}
