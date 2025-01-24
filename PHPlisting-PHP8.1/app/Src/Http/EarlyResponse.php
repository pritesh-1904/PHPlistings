<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Http;

class EarlyResponse
    extends Response
{

    protected $callback;

    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    public function send($content = null)
    {
        @set_time_limit(300);
        @ignore_user_abort(true);

        if (!ob_get_level()) {
            ob_start();
        }

        $this->sendContent();

        http_response_code($this->statusCode);

        $this->setHeaders([
            'Content-Encoding' => 'none',
            'Content-Length' => ob_get_length(),
            'Connection' => 'close',
        ]);

        $this->sendHeaders();

        ob_end_flush();
        flush();

        if (session_id()) {
            session_write_close();
        }

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        ob_start();

        if (is_callable($this->callback)) {
            call_user_func($this->callback);
        }

        ob_end_clean();

        return $this;
    }

}
