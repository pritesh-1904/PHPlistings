<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers;

class Error
    extends \App\Src\Mvc\BaseController
{

    public function __construct()
    {
        layout()
            ->setHeader('header')
            ->setFooter('footer')
            ->setWrapper('wrapper');
    }

    public function action404($params)
    {
        $page = \App\Models\Page::where('slug', 'error/404')->first();

        if (null === $page) {
            throw new \Exception();
        }

        layout()
            ->setTitle($page->title)
            ->setMeta('title', $page->meta_title)
            ->setMeta('keywords', $page->meta_keywords)
            ->setMeta('description', $page->meta_description);

        return response(
            layout()->content($page->render(collect(['page' => $page]))),
            404
        );
    }

    public function action405($params)
    {
        $page = \App\Models\Page::where('slug', 'error/405')->first();

        if (null === $page) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        layout()
            ->setTitle($page->title)
            ->setMeta('title', $page->meta_title)
            ->setMeta('keywords', $page->meta_keywords)
            ->setMeta('description', $page->meta_description);

        return response(
            layout()->content($page->render(collect(['page' => $page]))),
            405
        );
    }

    public function action500($params)
    {
        layout()->setTitle('500 Internal Server Error');

        return response(layout()->content(
            view('500', ['message' => ($params['message'] ?? '')])
        ), 500);
    }

}
