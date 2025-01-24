<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Http;

class RedirectResponse
    extends Response
{

    protected $redirectUrl;
    protected $statusCode = 302;

    public function to($redirectUrl, $statusCode = 302)
    {
        $this->headers->set('Location', $redirectUrl);
        $this->redirectUrl = $redirectUrl;
        $this->setStatusCode($statusCode);

        return $this;
    }

    public function with($key, $value)
    {
        session()->flash($key, $value);

        return $this;
    }

    public function send($content = null)
    {
        http_response_code($this->statusCode);

        $this->sendHeaders();

        return '<!DOCTYPE html>
        <html>
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="refresh" content="0;redirectUrl=' . $this->redirectUrl . '">
                <title>Auto-redirect to ' . $this->redirectUrl . '</title>
            </head>
            <body>
                Redirecting to <a href="' . $this->redirectUrl . '">' . $this->redirectUrl . '</a>
            </body>
        </html>
        ';
    }

}
