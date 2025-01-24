<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Http;

class Session
{

    public function __construct()
    {
        session_cache_limiter('');

        ini_set('session.cookie_path', '/' . trim(request()->basePath(), '/'));
        ini_set('session.gc_maxlifetime', (int) config()->session->maxlifetime);
        ini_set('session.httponly', true);

        $class = '\\App\\Src\\Http\\SessionHandler\\' . ucfirst(strtolower(config()->session->handler));
        if (class_exists($class) && is_subclass_of($class, '\\SessionHandlerInterface')) {
            session_set_save_handler(new $class(), true);
        } else {
            throw new \Exception('Session handler "' . config()->session->handler. '" is not supported');
        }

        if (!headers_sent() && session_status() == \PHP_SESSION_NONE) {
            session_start();
            $this->forget($this->get('__flash.old', []));
            $this->put('__flash.old', $this->get('__flash.new', []));
            $this->put('__flash.new', []);
        }
    }

    public function get($offset, $default = null)
    {
        if ($this->exists($offset)) {
            return $_SESSION[$offset];
        } else {
            return $default;
        }
    }

    public function exists($offset)
    {
        return array_key_exists($offset, $_SESSION);
    }

    public function has($offset)
    {
        return isset($_SESSION[$offset]);
    }

    public function put($offset, $value)
    {
        $_SESSION[$offset] = $value;

        return $this;
    }

    public function flash($offset, $value)
    {
        $this->put($offset, $value);
        $this->push('__flash.new', $offset);

        return $this;
    }

    protected function push($offset, $value)
    {
        $array = $this->get($offset, []);
        if (!in_array($value, $array)) {
            $array[] = $value;
            $this->put($offset, $array);
        }

        return $this;
    }

    public function forget($offset)
    {
        if (is_array($offset)) {
            foreach ($offset as $key) {
                $this->forget($key);
            }
        } else {
            unset($_SESSION[$offset]);
        }

        return $this;
    }

    public function reflash()
    {
        $this->put('__flash.new', $this->get('__flash.old', []));

        return $this;
    }

    public function flush()
    {
        return session_unset();
    }

}
