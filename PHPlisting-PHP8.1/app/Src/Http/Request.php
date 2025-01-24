<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Http;

class Request {

    protected $methods = [
        'OPTIONS',
        'GET',
        'HEAD',
        'POST',
        'PUT',
        'DELETE',
        'TRACE',
        'CONNECT',
    ];

    protected $url;

    public $get;
    public $post;
    public $server;
    public $files;

    protected $cookies;

    public function __construct()
    {
        $this->get = collect($_GET);
        $this->post = collect($_POST);
        $this->server = collect($_SERVER);
        $this->cookies = collect($_COOKIE);
        $this->files = collect();

        foreach ($_FILES as $index => $file) {
            if (!is_array($file['tmp_name'])) {
                $this->files->put($index, new \App\Src\Http\File\UploadedFile(
                    $file['tmp_name'],
                    $file['name'],
                    $file['type'] ?? null,
                    $file['size'] ?? null,
                    $file['error'] ?? null
                ));
            } else {
                $array = [];

                foreach ($file['tmp_name'] as $key => $value) {
                    $array[] = new \App\Src\Http\File\UploadedFile(
                        $file['tmp_name'][$key],
                        $file['name'][$key],
                        $file['type'][$key] ?? null,
                        $file['size'][$key] ?? null,
                        $file['error'][$key] ?? null
                    );
                }

                $this->files->put($index, $array);
            }
        }

        $port = $this->server['SERVER_PORT'];
        $protocol = ((isset($this->server['HTTPS']) && strtolower($this->server['HTTPS']) != 'off') || $port == 443) ? 'https' : 'http';
        $host = $this->server['HTTP_HOST'];
        $path = $this->path();
        $queryString = $this->server['QUERY_STRING'];

        $this->url = new Url($protocol . '://' . $host . (($port != 80 && $port != 443) ? ':' . $port : '') . $path . (($queryString != '') ? '?' . $queryString : ''));
    }          

    public function cookie($name)
    {
        if (isset($this->cookies->$name)) {
            return $this->cookies->$name;
        }

        return null;
    }

    public function session()
    {
        return session();
    }

    public function method()
    {
        if (is_string($this->server['REQUEST_METHOD']) && in_array(strtoupper($this->server['REQUEST_METHOD']), $this->methods)) {
            return strtoupper($this->server['REQUEST_METHOD']);
        } else {
            return 'GET';
        }
    }

    public function isMethod($method)
    {
        return (strtoupper($method) == $this->method());
    }

    public function path($basePath = false)
    {
        return rawurldecode(preg_replace('/\/+$/', '', (parse_url( ( ( false !== $basePath ) ? trim(config()->app->url, '/') : $this->server['REQUEST_URI'] ), \PHP_URL_PATH) ?? '')));
    }

    public function basePath()
    {
        return $this->path(true);
    }

    public function relativePath()
    {
        return '/' . trim(substr($this->path(), strlen($this->basePath())), '/');
    }

    public function root()
    {
        if ('' !== config()->app->url) {
            return trim(config()->app->url, '/');
        }

        return (($this->url->scheme != '') ? $this->url->scheme : 'http') . '://' . $this->url->host . str_replace('/index.php', '', $this->server['SCRIPT_NAME']);
    }

    public function route($route, array $query = null, $separator = '&')
    {
        return $this->root() . '/' . trim($route, '/') . ((null !== $query && count(array_filter($query)) > 0) ? '?' . http_build_query(array_filter($query), '', $separator) : '');
    }

    public function url()
    {
        return $this->url;
    }

    public function urlWithQuery(array $query = null)
    {
        if (null !== $query) {
            $url = clone $this->url;
            $url->query->collect($query);
        } else {
            $url = $this->url;
        }

        return $url;
    }

    public function userAgent()
    {
        return get_browser();
    }

    public function ip()
    {
        foreach ([
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ] as $key){
            if (null !== $this->server->get($key)) {
                foreach (explode(',', $this->server->get($key)) as $ip) {
//                    if (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    if (false !== filter_var($ip, FILTER_VALIDATE_IP)) {
                        if (false !== filter_var($ip, FILTER_VALIDATE_IP)) {
                            return $ip;
                        }
                    }
                }
            }
        }
    }

    public function isBanned()
    {
        $array = array_filter(array_map('trim', explode("\n", config()->other->banned_ips)));

        $ip = $this->ip();

        if ('' != $ip) {
            if (in_array($ip, $array)) {
                return true;
            }

            foreach ($array as $fragment) {
                $pos = strpos($fragment, '*');

                if (false !== $pos && substr($fragment, 0, $pos) . '*' == $fragment) {
                    return true;
                }
            }
        }

        return false;
    }

}
