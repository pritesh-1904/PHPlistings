<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Http;

class Response
{

    protected $statusCode = 200;
    protected $content;
    protected $headers;

    public function __construct(\App\Src\Http\ResponseHeaders $headers)
    {
        $this->headers = $headers;

        $this->setHeaders([
            'Expires' => 'Tue, 1 Jan 1980 00:00:01 GMT',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function cookie($name, $value, $minutes = 0, $path = '/', $domain = null, $secure = false, $httpOnly = false, $sameSite = 'lax')
    {
        $this->headers->setCookie(new Cookie($name, $value, (($minutes != 0) ? time() + $minutes * 60 : 0), $path, $domain, $secure, $httpOnly, $sameSite));

        return $this;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setHeader($name, $value, $replace = true)
    {
        $this->headers->set($name, $value, $replace);

        return $this;
    }

    public function setHeaders(array $headers = [], $replace = true)
    {
        foreach ($headers as $name => $value) {
            $this->headers->set($name, $value, $replace);
        }

        return $this;
    }

    public function withHeaders(array $headers = [], $replace = true)
    {
        return $this->setHeaders(...func_get_args());
    }

    public function sendHeaders()
    {
        if (!headers_sent()) {            
            foreach ($this->headers->get() as $name => $values) {
                foreach ($values as $value) {
                    if (null === $value[0] && false !== $value[1]) {
                        header_remove($name);
                    } else {
                        header($name . ': ' . $value[0], $value[1], $this->statusCode);
                    }
                }
            }
        }

        return $this;
    }

    public function sendContent($content = null)
    {
        echo $content ?? $this->content;

        return $this;
    }

    public function send($content = null)
    {
        http_response_code($this->statusCode);
        $this->sendHeaders();        
        $this->sendContent($content);

        return $this;
    }

}
