<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

function app($offset)
{
    return \App\Src\Application::getInstance()->container->$offset;
}

function collect(array $array = [], $recursive = false)
{
    return new \App\Src\Support\Collection($array, $recursive);
}

function is_collection($value)
{
    return ($value instanceof \App\Src\Support\Collection);
}

function config()
{
    return app('config');
}

function db()
{
    return app('db');
}

function __($key, $replace = [], $number = 1, $locale = null)
{
    return app('translator')->translate(...func_get_args());
}

function _e($key, $replace = [], $number = 1, $locale = null)
{
    echo __(...func_get_args());
}

function e($value, $doubleEncode = false)
{
    return \htmlspecialchars($value ?? '', \ENT_QUOTES, 'UTF-8', $doubleEncode);
}

function d($value)
{
    return \html_entity_decode($value ?? '', \ENT_QUOTES);
}

function dd($value)
{
    var_dump($value); die();
}

function route($route, $query = null, $locale = null, $separator = '&')
{
    $route = trim($route, '/');
    
    $route = app('locale')->getLocalizedPath($route, $locale);

    if (false !== config()->app->locale_url_default_exclude && 
        ($locale == app('locale')->getDefault() || 
            (null === $locale && app('locale')->getLocale() == app('locale')->getDefault())
        )
    ) {
        $route = app('locale')->getNonLocalizedPath($route);
    }

    return app('request')->route($route, $query, $separator);
}

function adminRoute($route, $query = null, $locale = null, $separator = '&')
{
    $route = trim(config()->app->admin_directory, '/') . '/' . trim($route, '/');

    return route($route, $query, $locale, $separator);
}

function getRoute($localized = false)
{
    $route = (false === $localized) ? app('locale')->getNonLocalizedPath(app('request')->relativePath()) : app('locale')->getLocalizedPath(app('request')->relativePath());
    
    return trim($route, '/');
}

function routeMatch($pattern, array $query = null, $localized = false)
{
    $pattern = trim($pattern, '/');

    $fragments = explode('/', getRoute($localized));

    if (count(explode('/', $pattern)) != count($fragments)) {
        return false;
    }

    foreach (explode('/', $pattern) as $key => $fragment) {
        if (!isset($fragments[$key]) || ($fragment != '*' && $fragment != $fragments[$key])) {
            return false;
        }
    }

    if (null !== $query) {
        foreach ($query as $key => $value) {
            if (null === request()->get->get($key) || request()->get->get($key) != $value) {
                return false;
            }
        }
    }

    return true;    
}

function adminRouteMatch($pattern, array $query = null, $localized = false)
{
    $pattern = trim(config()->app->admin_directory, '/') . '/' . trim($pattern, '/');
        
    return routeMatch($pattern, $query, $localized);
}

function locale()
{
    return app('locale');
}

function request()
{
    return app('request');
}

function response($content = null, $statusCode = 200)
{
    return app('response')
        ->setContent($content)
        ->setStatusCode($statusCode);
}

function earlyResponse()
{
    return app('earlyResponse');
}

function fileResponse($file = null)
{
    $response = app('fileResponse');

    if (null !== $file) {
        $response->file($file);
    }

    return $response;
}

function redirect($url, $statusCode = 302)
{
    return app('redirect')->to($url, $statusCode);
}

function cache($parameters = null, $seconds = 0)
{
    if (null === $parameters) {
        return app('cache');
    } else if (is_array($parameters)) {
        foreach($parameters as $parameter) {
            if (!is_array($parameter[0])) {
                app('cache')->put($parameter[0], $parameter[1], $seconds);
            }
            else {
                throw new \Exception('Cache offset name must be a string');
            }
        }
    } else if (is_string($parameters)) {
        return app('cache')->get($parameters);
    }
}

function session($value = null, $default = null)
{
    if (null !== $value) {
        if (is_array($value) && count($value) == 2) {
            return app('session')->put($value[0], $value[1]);
        }
        return app('session')->get($value, $default);
    } else {
        return app('session');
    }
}

function auth()
{
    return app('auth');
}

function form(\App\Src\Orm\Model $model = null, $type = 'submit')
{
    $form = app('form');

    if (null !== $model) {
        $form->bindModel($model, $type);
    }

    return $form;
}

function asset($path, $query = null)
{
    return app('request')->route($path, $query);
}

function theme()
{
    return app('theme');
}

function layout()
{
    return app('layout');
}

function view($template, $data = [], $extension = 'html.php')
{
    return app('view')->render($template, $data, $extension);
}

function dataTable($data)
{
    return new \App\Src\Support\DataTable($data);
}

function slugify($string, $separator = null)
{
    return \Cocur\Slugify\Slugify::create()->slugify(d($string), $separator);
}

function purify($string)
{
    $config = \HTMLPurifier_Config::createDefault();
    $config->set('Core.Encoding', 'UTF-8');
    $config->set('Cache.SerializerPath', ROOT_PATH_PROTECTED . DS . 'Cache');
    $config->set('HTML.Allowed', 'h1[style],h2[style],h3[style],h4[style],h5[style],h6[style],strong,b,i,em,ul[style],ol[style],li[style],p[style],pre,span[style],br,a[href|rel|target|title|style],blockquote[style]');
    $config->set('HTML.TargetBlank', true);
    $config->set('HTML.Nofollow', true);

    $purifier = new \HTMLPurifier($config);

    return $purifier->purify($string);
}

function minify($string)
{
    $minify = new \MatthiasMullie\Minify\CSS($string);

    return $minify->minify();
}

function attr(array $array = [])
{
    return new \App\Src\Html\Attributes($array);
}

function qrcode($string, $version = 5, $scale = 5)
{
    $options = new \chillerlan\QRCode\QROptions([
        'version' => $version,
        'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel' => \chillerlan\QRCode\QRCode::ECC_Q,
        'scale' => $scale,
        'imageBase64'  => true
    ]);

    return (new \chillerlan\QRCode\QRCode($options))->render($string);
}
