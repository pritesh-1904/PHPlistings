<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers\Account;

class Bookmarks
    extends \App\Controllers\Account\BaseController
{

    public function actionIndex($params)
    {
        if (null === $page = \App\Models\Page::where('slug', 'account/bookmarks')->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if ((null !== config()->general->maintenance || null === $page->active) && false === auth()->check(['admin_login', 'admin_appearance'])) {
            return redirect(route('maintenance'), 302);
        }

        layout()
            ->setTitle($page->title)
            ->setMeta('title', $page->meta_title)
            ->setMeta('keywords', $page->meta_keywords)
            ->setMeta('description', $page->meta_description);
        
        if (null !== request()->post->get('action') && null !== request()->post->get('id')) {
            foreach ((array) request()->post->get('id') as $id) {
                auth()->user()->bookmarks()->detach($id);
            }

            return redirect(route('account/bookmarks', session()->get('account/bookmarks')))
                ->with('success', view('flash/success', ['message' => __('bookmark.alert.delete.success', [], 2)]));
        }

        $listings = auth()->user()->bookmarks()
            ->withPivot(['id'])
            ->whereNotNull('active')
            ->where('status', 'active')
            ->orderBy('pivot_id', 'desc')
            ->paginate();

        $table = dataTable($listings)
            ->addColumns([
                'title' => [__('bookmark.datatable.label.title')],
            ])
            ->addActions([
                'view' => [__('bookmark.datatable.action.view_listing'), function ($listing) {
                    return route($listing->type->slug . '/' . $listing->slug);
                }],
                'delete' => [__('bookmark.datatable.action.delete'), function ($listing) {
                    return route('account/bookmarks/delete/' . $listing->slug);
                }],
            ])
            ->addBulkActions([
                'delete' => __('bookmark.datatable.action.delete'),
            ]);

        $data = collect([
            'page' => $page,
            'html' => view('account/bookmarks/index', [
                'listings' => $table,
            ]),
        ]);

        $response = $page->render($data);

        if ($response instanceof \App\Src\Http\RedirectResponse) {
            return $response;
        }

        return response(
            layout()->content($response)
        );
    }

    public function actionDelete($params)
    {
        if (false === isset($params['slug']) || null === $listing = \App\Models\Listing::where('slug', $params['slug'])->first()) {
            return redirect(route('account/bookmarks'));
        }

        auth()->user()->bookmarks()->detach($listing->id);

        return redirect(route('account/bookmarks', session()->get('account/bookmarks')))
            ->with('success', view('flash/success', ['message' => __('bookmark.alert.delete.success', [], 1)]));
    }

}
