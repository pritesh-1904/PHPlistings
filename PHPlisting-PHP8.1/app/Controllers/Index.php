<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers;

class Index
    extends \App\Src\Mvc\BaseController
{

    public function actionRouter($params)
    {
        $data = collect(['params' => $params]);

        if (false !== auth()->check()) {
            $data->bookmarks = db()->table('bookmarks')
                ->where('user_id', auth()->user()->id)
                ->get(['listing_id']);
        }

        $replacements = [];

        $category = null;
        $location = null;

        if (count($params) == 0) {
            if (null !== \App\Models\Page::where('slug', 'index')->first()) {
                $data->slug = 'index';
            } else {
                $query = \App\Models\Type::whereNull('deleted');

                if (false === auth()->check('admin_login')) {
                    $query->whereNotNull('active');
                }
                                
                $type = $query->orderBy('weight')->first();

                if (null === $type) {
                    return redirect(route('maintenance'), 302);
                }

                $data->type = $type;
                $data->slug = 'type/index';

                $replacements['type_plural'] = $type->name_plural;
                $replacements['type_singular'] = $type->name_singular;

                if (session()->has('_search_' . $type->slug)) {
                    session()->forget('_search_' . $type->slug);
                }
            }
        } else {
            if ('index' == $params['slug']) {
                throw new \App\Src\Http\NotFoundHttpException();
            }

            $type = \App\Models\Type::where('slug', $params['slug'])->whereNull('deleted')->first();

            $data->type = $type;

            if (null === $type) {
                $data->slug = $params['slug'];

                if (isset($params['first'])) {
                    $data->slug .= '/' . $params['first'];
                }

                if (isset($params['second'])) {
                    $data->slug .= '/' . $params['second'];
                }
            } else {
                if (null === $type->active && false === auth()->check('admin_login')) {
                    return redirect(route('maintenance'), 302);
                }
                
                $data->slug = 'type/index';

                $replacements['type_plural'] = $type->name_plural;
                $replacements['type_singular'] = $type->name_singular;

                if (isset($params['first'])) {
                    if ($params['first'] == 'search' || ((null !== request()->get->get('category_id') && is_numeric(request()->get->get('category_id'))) || (null !== request()->get->get('location_id') && is_numeric(request()->get->get('location_id'))))) {
                        $data->slug = 'type/' . $params['first'];

                        if ($params['first'] != 'search') {
                            $category = \App\Models\Category::where('slug', $params['first'])->where('type_id', $type->id)->first();

                            if (isset($params['second'])) {
                                $location = \App\Models\Location::where('slug', $params['second'])->first();
                            } else if (null === $category) {
                                $location = \App\Models\Location::where('slug', $params['first'])->first();
                            }
                        }

                        if (null !== request()->get->get('category_id') && is_numeric(request()->get->get('category_id'))) {
                            $category = \App\Models\Category::find(request()->get->get('category_id'));
                        }

                        if (null !== request()->get->get('location_id') && is_numeric(request()->get->get('location_id'))) {
                            $location = \App\Models\Location::find(request()->get->get('location_id'));
                        }

                        if ((isset($category) && null !== $category) || (isset($location) && null !== $location)) {
                            request()->get->collect([
                                'submit' => null,
                                'category_id' => null,
                                'location_id' => null,
                            ]);

                            $route = [$type->slug];

                            if (isset($category) && null !== $category && false === $category->isRoot()) {
                                $route[] = $category->slug;
                            }

                            if (isset($location) && null !== $location && false === $location->isRoot()) {
                                $route[] = $location->slug;
                            }

                            if (count($route) == 1) {
                                $route[] = 'search';
                            }

                            return redirect(route(implode('/', $route), request()->get->all()), 301);
                        }
                    } else {
                        if (in_array($params['first'], [
                            'pricing',
                        ])) {
                            $data->slug = 'type/' . $params['first'];
                        } else if (null !== $category = \App\Models\Category::where('slug', $params['first'])->where('type_id', $type->id)->first()) {
                            $data->category = $category;
                            $data->slug = 'type/category';

                            $replacements['category'] = $category->name;

                            (new \App\Repositories\Statistics)->push('category_impression', $category->id);

                            if (isset($params['second'])) {
                                if (null !== $location = \App\Models\Location::where('slug', $params['second'])->first()) {
                                    $data->category = $category;
                                    $data->location = $location;
                                    $data->slug = 'type/category/location';

                                    $replacements['location'] = $location->name;

                                    if (null === $locations = cache()->db()->get('location-ancestors.' . $location->id . '.' . $location->get('_left') . '.' . $location->get('_right') . '.' . locale()->getLocale())) {
                                        $locations = $location->ancestorsAndSelf()->get(['id', 'slug', 'name'])->all();
                                        cache()->db()->put('location-ancestors.' . $location->id . '.' . $location->get('_left') . '.' . $location->get('_right') . '.' . locale()->getLocale(), $locations, 3600);
                                    }

                                    $counter = 0;

                                    foreach ($locations as $location) {
                                        $replacements['location_' . $counter] = $location->get('name');

                                        $counter++;
                                    }

                                    (new \App\Repositories\Statistics)->push('location_impression', $location->id);
                                } else {
                                    throw new \App\Src\Http\NotFoundHttpException();
                                }
                            }

                            layout()
                                ->setMeta('title', $category->meta_title, $replacements)
                                ->setMeta('keywords', $category->meta_keywords, $replacements)
                                ->setMeta('description', $category->meta_description, $replacements);
                        } else if (null !== $location = \App\Models\Location::where('slug', $params['first'])->first()) {
                            $data->location = $location;
                            $data->slug = 'type/location';

                            $replacements['location'] = $location->name;

                            if (null === $locations = cache()->db()->get('location-ancestors.' . $location->id . '.' . $location->get('_left') . '.' . $location->get('_right') . '.' . locale()->getLocale())) {
                                $locations = $location->ancestorsAndSelf()->get(['id', 'slug', 'name'])->all();
                                cache()->db()->put('location-ancestors.' . $location->id . '.' . $location->get('_left') . '.' . $location->get('_right') . '.' . locale()->getLocale(), $locations, 3600);
                            }

                            $counter = 0;

                            foreach ($locations as $location) {
                                $replacements['location_' . $counter] = $location->get('name');

                                $counter++;
                            }

                            layout()
                                ->setMeta('title', $location->meta_title, $replacements)
                                ->setMeta('keywords', $location->meta_keywords, $replacements)
                                ->setMeta('description', $location->meta_description, $replacements);

                            (new \App\Repositories\Statistics)->push('location_impression', $location->id);
                        } else if (
                            null !== $listing = \App\Models\Listing::where('slug', $params['first'])
                                ->where('type_id', $type->id)
                                ->first()
                        ) {

                            if (false === auth()->check() || (false === auth()->check('admin_login') && auth()->user()->id != $listing->user_id)) {
                                if ($listing->status != 'active' || null === $listing->active) {
                                    throw new \App\Src\Http\NotFoundHttpException();
                                }
                            }

                            $data->listing = $listing;
                            $data->slug = 'type/listing';

                            $replacements['listing'] = $listing->title;

                            $replacements['category'] = $listing->category->name;

                            if (null === $categories = cache()->db()->get('category-ancestors.' . $listing->category->id . '.' . $listing->category->get('_left') . '.' . $listing->category->get('_right') . '.' . locale()->getLocale())) {
                                $categories = $listing->category->ancestorsAndSelfWithoutRoot()->get(['id', 'slug', 'name'])->all();
                                cache()->db()->put('category-ancestors.' . $listing->category->id . '.' . $listing->category->get('_left') . '.' . $listing->category->get('_right') . '.' . locale()->getLocale(), $categories, 3600);
                            }

                            $counter = 1;

                            foreach ($categories as $category) {
                                $replacements['category_' . $counter] = $category->get('name');

                                $counter++;
                            }

                            if (null !== $type->localizable) {
                                if (null !== $listing->get('_address')) {
                                    $replacements['full_address'] = e($listing->getOutputableValue('_address'));
                                    $replacements['address'] = e($listing->get('address'));
                                    $replacements['zip'] = e($listing->get('zip'));
                                }

                                $replacements['location'] = $listing->location->name;

                                if (null === $locations = cache()->db()->get('location-ancestors.' . $listing->location->id . '.' . $listing->location->get('_left') . '.' . $listing->location->get('_right') . '.' . locale()->getLocale())) {
                                    $locations = $listing->location->ancestorsAndSelf()->get(['id', 'slug', 'name'])->all();
                                    cache()->db()->put('location-ancestors.' . $listing->location->id . '.' . $listing->location->get('_left') . '.' . $listing->location->get('_right') . '.' . locale()->getLocale(), $locations, 3600);
                                }

                                $counter = 0;

                                foreach ($locations as $location) {
                                    $replacements['location_' . $counter] = $location->get('name');

                                    $counter++;
                                }
                            }

                            if (isset($params['second'])) {
                                if (in_array($params['second'], [
                                    'send-message',
                                    'add-review',
                                    'reviews',
                                    'claim',
                                    'visit-website',
                                ])) {

                                    if ('visit-website' == $params['second']) {
                                        $field = $listing->data->where('field_name', 'website')->first();

                                        if (null === $field || null === $field->get('active') || '' == $field->get('value', '')) {
                                            throw new \App\Src\Http\NotFoundHttpException();
                                        }

                                        (new \App\Repositories\Statistics)->push('listing_website_click', $listing->id);

                                        return redirect($field->get('value'));
                                    } else {
                                        if (in_array($params['second'], [
                                            'send-message',
                                            'add-review',
                                            'claim',
                                        ])) {
                                            if (false === auth()->check('user_login')) {
                                                return redirect(route('account/login'))
                                                    ->with('error', view('flash/error', ['message' => [__('account.alert.login_required')]]))
                                                    ->with('return', route($type->slug . '/' . $listing->slug . '/' . $params['second']));
                                            }
                                        }

                                        if ('send-message' == $params['second'] && null === $listing->get('_send_message')) {
                                            throw new \App\Src\Http\NotFoundHttpException();
                                        }

                                        if (in_array($params['second'], ['add-review', 'reviews']) && null === $listing->get('_reviews')) {
                                            throw new \App\Src\Http\NotFoundHttpException();
                                        }

                                        if ('claim' == $params['second'] && null !== $listing->get('claimed')) {
                                            throw new \App\Src\Http\NotFoundHttpException();
                                        }

                                        $data->slug = 'type/listing/' . $params['second'];
                                    }
                                } else {
                                    throw new \App\Src\Http\NotFoundHttpException();
                                }
                            } else {
                                if (null === $listing->get('_page')) {
                                    throw new \App\Src\Http\NotFoundHttpException();
                                }
                                
                                (new \App\Repositories\Statistics)->push('listing_impression', $listing->id);

                                if (null !== $listing->get('_seo')) {
                                    layout()    
                                        ->setMeta('title', e($listing->meta_title))
                                        ->setMeta('keywords', e($listing->meta_keywords))
                                        ->setMeta('description', e($listing->meta_description));
                                }

                                layout()
                                    ->setMetaProperty('og:site_name', config()->general->site_name)
                                    ->setMetaProperty('og:title', $listing->getOutputableValue('_title'))
                                    ->setMetaProperty('og:description', $listing->getOutputableValue('_short_description'))
                                    ->setMetaProperty('og:type', 'business:business')
                                    ->setMetaProperty('og:url', route($listing->type->slug . '/' . $listing->slug))
                                    ->setMeta('geo.position', e($listing->latitude . ';' . $listing->longitude))
                                    ->setMetaProperty('place:location:latitude', e($listing->latitude))
                                    ->setMetaProperty('place:location:longitude', e($listing->longitude));

                                $field = $listing->data->where('field_name', 'logo_id')->first();

                                if (null !== $field && null !== $field->active && '' != $field->value && null !== $logo = \App\Models\File::where('document_id', $field->value)->where('uploadtype_id', 1)->first()) {
                                    layout()
                                        ->setMetaProperty('og:image', $logo->getUrl())
                                        ->setMetaProperty('og:image:type', $logo->mime)
                                        ->setMetaProperty('og:image:alt', $listing->getOutputableValue('_title'));
                                }
                            }
                        } else {
                            throw new \App\Src\Http\NotFoundHttpException();
                        }
                    }
                }

                if ($data->slug == 'type/index') {
                    if (session()->has('_search_' . $type->slug)) {
                         session()->forget('_search_' . $type->slug);
                    }
                }
            }
        }

        $query = \App\Models\Page::where('slug', $data->slug);

        if (null !== $data->get('type')) {
            $query->where('type_id', $data->type->id);
        }

        if (null === $page = $query->first()) {
            throw new \App\Src\Http\NotFoundHttpException();
        }

        if (
            'maintenance' != $page->slug 
            && (null !== config()->general->maintenance || null === $page->active) 
            && false === auth()->check(['admin_login', 'admin_appearance'])
        ) {
            return redirect(route('maintenance'), 302);
        }

        $data->page = $page;

        $canonicalRoute = getRoute();

        if ('type/index' == $data->slug && null !== $data->type && false !== $data->type->isPrimary()) {
            if (null === $index = \App\Models\Page::whereNotNull('active')->where('slug', 'index')->first(['id'])) {
                $canonicalRoute = '';
            }
        }

        layout()
            ->setTitle($page->title, $replacements)
            ->setCanonicalRoute($canonicalRoute, request()->get)
            ->setMetaIfEmpty('title', $page->meta_title, $replacements)
            ->setMetaIfEmpty('keywords', $page->meta_keywords, $replacements)
            ->setMetaIfEmpty('description', $page->meta_description, $replacements);

        $response = $page->render($data);

        if ($response instanceof \App\Src\Http\Response) {
            return $response;
        }

        return response(
            layout()->content($response)
        );
    }

}
