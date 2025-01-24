<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers;

class Ajax
    extends \App\Src\Mvc\BaseController
{

    public function actionAi($params)
    {
        if (false !== $this->isAccount() && null !== request()->post->get('request')) {
            $endpoint = 'https://api.openai.com/v1/chat/completions';

            $headers = array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . config()->other->openai_api_key
            );

            $data = [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'user',   
                        'content' => request()->post->get('request')
                    ],
                ],
                'max_tokens' => 500,
                'temperature' => 0.9,
                'n' => 1
            ];

            if (false === $this->isAdmin()) {
                if (config()->other->openai_daily_limit < $count = (new \App\Repositories\Log())->getTodayRecords('openai_request', auth()->user()->id)->count()) {
                    return ['error' => __('form.label.chat_gpt.daily_quota_exceeded', ['count' => config()->other->openai_daily_limit])];
                }
            }

            $ch = curl_init();

            curl_setopt($ch, \CURLOPT_URL, $endpoint);
            curl_setopt($ch, \CURLOPT_POST, 1);
            curl_setopt($ch, \CURLOPT_POSTFIELDS, json_encode($data, true));
            curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, \CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $responseCode = curl_getinfo($ch, \CURLINFO_RESPONSE_CODE);

            if (curl_errno($ch) || 200 != $responseCode) {
                return ['error' => __('form.label.chat_gpt.request_error')];
            }

            curl_close($ch);

            (new \App\Repositories\Log())->push('openai_request', auth()->user()->id);

            if (null !== $decodedJSON = json_decode($response, true)) {
                if (false !== isset($decodedJSON['choices'][0]['message']['content'])) {
                    return ['response' => purify(d($decodedJSON['choices'][0]['message']['content']))];
                }
            }
        }

        return ['error' => __('form.label.chat_gpt.request_error')];
    }

    public function actionUploadAsset($params)
    {
        if (null !== $uploadedFile = request()->files->get('upload')) {
            if (false !== $this->isAdmin()) {
                $uploadedFile->store(PATH . DS . 'assets' . DS . $uploadedFile->getClientFileName());

                return [
                    'uploaded' => 1,
                    'filename' => $uploadedFile->getClientFileName(),
                    'url' => asset('assets/' . $uploadedFile->getClientFileName()),
                ];
            } else {
                return [
                    'uploaded' => 0,
                    'error' => [
                        'message' => 'Authentication is required',
                    ],
                ];
            }
        } else {
            return [
                'uploaded' => 0,
                'error' => [
                    'message' => 'No file is uploaded',
                ],
            ];
        }
    }

    public function actionBookmark($params)
    {
        $state = 'off';
        
        if (false !== $this->isAccount() && null !== request()->post->get('id')) {
            if (db()->table('bookmarks')
                ->where('user_id', auth()->user()->id)
                ->get(['listing_id'])
                ->contains('listing_id', (int) request()->post->get('id'))
            ) {
                auth()->user()->bookmarks()->detach((int) request()->post->get('id'));
            } else {
                auth()->user()->bookmarks()->attach((int) request()->post->get('id'));
                $state = 'on';
            }
        }

        return response(view('misc/bookmark', ['state' => $state, 'type' => request()->post->get('type')]));
    }

    public function actionClickToCall($params)
    {
        if (null !== request()->post->get('id')) {
            if (null !== $listing = \App\Models\Listing::where('id', request()->post->get('id'))->first()) {
                (new \App\Repositories\Statistics)->push('listing_phone_view', (int) request()->post->get('id'));

                $field = $listing->data->where('field_name', 'phone')->first();

                return response($field->value);
            }
        }

        return response('');
    }

    public function actionVisitWebsite($params)
    {
        if (null !== request()->post->get('id')) {
            (new \App\Repositories\Statistics)->push('listing_website_click', (int) request()->post->get('id'));
        }
        
        return response();
    }

    public function actionMap($params)
    {
        if (null !== request()->post->get('type_id')
            && null !== request()->post->get('north')
            && null !== request()->post->get('east')
            && null !== request()->post->get('south')
            && null !== request()->post->get('west')
        ) {
        
            $type = \App\Models\Type::find(request()->post->get('type_id'));

            if (null !== $type && null === $type->deleted) {        
                $response = [
                    'type' => 'FeatureCollection',
                    'features' => [
                    ],
                ];

                $polygon = [];

                $polygon[] = request()->post->get('west') . ' ' . request()->post->get('south');
                $polygon[] = request()->post->get('west') . ' ' . request()->post->get('north');
                $polygon[] = request()->post->get('east') . ' ' . request()->post->get('north');
                $polygon[] = request()->post->get('east') . ' ' . request()->post->get('south');
                $polygon[] = request()->post->get('west') . ' ' . request()->post->get('south');

                $query = \App\Models\Listing::query()
                    ->whereNotNull('_map')
                    ->where('type_id', request()->post->get('type_id'))
                    ->where('active', '1')
                    ->where('status', 'active')
                    ->where(db()->raw('ST_Within(POINT(longitude, latitude), ST_GeomFromText(\'POLYGON((' . implode(', ', $polygon) . '))\'))'))
                    ->with([
                        'type',
                        'category',
                    ]);

                if (null !== $type->get('localizable')) {
                    $query->with('location');
                }

                foreach ($query->limit(100)->get() as $listing) {
                    $response['features'][] = [
                        'type' => 'Feature',
                        'geometry' => [
                            'type' => 'Point',
                            'coordinates' => [$listing->longitude, $listing->latitude],
                        ],
                        'properties' => [
                            'icon_color' => $listing->category->icon_color ?? 'white',
                            'marker_color' => $listing->category->marker_color ?? 'red',
                            'class' => $listing->category->icon,
                            'popup' => '
                                <p class="m-0 my-2">' . $listing->getOutputableValue('_category') . '</p>
                                <p class="text-medium m-0 my-2 display-10">' . $listing->getOutputableValue('_title') . '</p>
                                ' . (null !== $listing->type->localizable && null !== $listing->get('_address') ?
                                    '<p class="text-secondary m-0 mb-3"><i class="fas fa-map-marker-alt pr-2 text-danger"></i>' .  $listing->getOutputableValue('_address') . '</p>'
                                    : '') . '
                            <a class="btn btn-outline-primary btn-sm" href="' . route($listing->type->slug . '/' . $listing->slug) . '">' . e(__('listing.search.block.label.read_more')) . '</a>',
                        ],
                    ];
                }

                return response(json_encode($response));
            }
        }
    }

    public function actionHours($params)
    {
        if (false === $this->isAccount()) {
            return '';
        }

        if (null !== request()->post->get('action') && null !== request()->post->get('hash') && '' !== request()->post->get('hash')) {
            if ('post' == request()->post->get('action')) {
                $form = form()
                    ->add('dow', 'number', ['label' => __('hour.label.dow'), 'constraints' => 'required|min:1|max:7'])
                    ->add('start', 'time', ['label' => __('hour.label.from'), 'constraints' => 'required'])
                    ->add('end', 'time', ['label' => __('hour.label.to'), 'constraints' => 'required'])
                    ->forceRequest();

                $input = $form->getValues();

                if (strtotime($input->get('start')) > strtotime($input->get('end'))) {
                    $form->setValidationError('end', __('hour.alert.start_after_end'));
                }

                if (strtotime($input->get('start')) == strtotime($input->get('end'))) {
                    $form->setValidationError('end', __('hour.alert.start_equals_end'));
                }

                if ($form->isValid()) {
                    $hour = new \App\Models\Hour();

                    $hour->forceFill([
                        'hash' => request()->post->get('hash'),
                        'dow' => $input->dow,
                        'start_time' => $input->start,
                        'end_time' => $input->end,
                    ]);

                    $hour->save();
                } else {
                    $alert = view('flash/error', ['message' => $form->getValidationErrors()]);
                }
            } else if ('delete' == request()->post->get('action') && null !== request()->post->get('id') && request()->post->get('id') > 0) {
                \App\Models\Hour::query()
                    ->where('hash', request()->post->get('hash'))
                    ->where('id', request()->post->get('id'))
                    ->delete();

                $alert = view('flash/success', ['message' => __('hour.alert.delete.success')]);
            }

            return response(
                ($alert ?? null) . 
                view('misc/hours', [
                    'items' => \App\Models\Hour::query()
                        ->where('hash', request()->post->get('hash'))
                        ->orderBy('dow')
                        ->orderBy('start_time')
                        ->get()
                ])
            );
        }
    }

    public function actionUser($params)
    {
        if (false === $this->isAdmin()) {
            return [
                'results' => [],
                'pagination' => [
                    'more' => (boolean) false,
                ]
            ];
        }

        $users = \App\Models\User::query()
            ->where(db()->raw('CONCAT(first_name, \' \', last_name) LIKE ?', ['%' . e(request()->get->get('term')) . '%']))
            ->paginate();

        $results = [];
        
        foreach ($users as $user) {
            $results[] = ['id' => $user->id, 'text' => $user->getNameWithId()];
        }

        return [
            'results' => $results,
            'pagination' => [
                'more' => (boolean) (request()->get->get('page', 1) < ceil($users->getTotal() / $users->getLimit()) ? true : false),
            ],
        ];
    }

    public function actionListing($params)
    {
        if (false === $this->isAccount()) {
            return [
                'results' => [],
                'pagination' => [
                    'more' => (boolean) false,
                ]
            ];
        }

        $query = \App\Models\Listing::query();

        if (false === $this->isAdmin()) {
            $query->where('user_id', auth()->user()->id);
        }
        
        if (null !== request()->get->get('type')) {
            $query->where('type_id', request()->get->get('type'));
        }

        $query->whereHas('type', function($query) {
            $query->whereNull('deleted');
        });

        if (false === $this->isAdmin()) {
            $query->whereHas('type', function($query) {
                $query->whereNotNull('active');
            });
        }

        $listings = $query
            ->where(db()->raw('title LIKE ?', ['%' . e(request()->get->get('term')) . '%']))
            ->paginate();

        $results = [];
        
        foreach ($listings as $listing) {
            $results[] = ['id' => $listing->id, 'text' => $listing->title . ' (id: ' . $listing->id . ')'];
        }

        return [
            'results' => $results,
            'pagination' => [
                'more' => (boolean) (request()->get->get('page', 1) < ceil($listings->getTotal() / $listings->getLimit()) ? true : false),
            ],
        ];
    }

    public function actionCategory($params)
    {
        if ('' == request()->get->get('type_id', '') || null === request()->get->get('term') || mb_strlen(request()->get->get('term'), 'UTF-8') < 3) {
            return [
                'results' => [],
                'pagination' => [
                    'more' => (boolean) false,
                ]
            ];
        }
        
        $query = \App\Models\Category::query()
            ->where('type_id', request()->get->get('type_id'));

        if (config()->app->locale_fallback == locale()->getLocale()) {
            $query->where(db()->raw('name REGEXP ?', ['"' . locale()->getLocale() . '":"' . e(request()->get->get('term')) . '([^"])*"']));
        } else {
            $query->where(function ($query) {
                $query
                    ->where(db()->raw('name REGEXP ?', ['"' . locale()->getLocale() . '":"' . e(request()->get->get('term')) . '([^"])*"']))
                    ->orWhere(db()->raw('name REGEXP ?', ['"' . config()->app->locale_fallback . '":"' . e(request()->get->get('term')) . '([^"])*"']));
            });
        }

        $categories = $query
            ->orderBy('_left')
            ->paginate();

        $results = [];
        
        foreach ($categories as $category) {
            $results[] = ['id' => $category->id, 'text' => $category->ancestorsAndSelfWithoutRoot()->get(['id', 'name'])->pluck('name')->implode(' &raquo; ')];
        }

        return [
            'results' => $results,
            'pagination' => [
                'more' => (boolean) (request()->get->get('page', 1) < ceil($categories->getTotal() / $categories->getLimit()) ? true : false),
            ],
        ];
    }

    public function actionLocation($params)
    {
        if (null === request()->get->get('term') || mb_strlen(request()->get->get('term'), 'UTF-8') < 3) {
            return [
                'results' => [],
                'pagination' => [
                    'more' => (boolean) false,
                ]
            ];
        }
        
        $query = \App\Models\Location::query();

        if (config()->app->locale_fallback == locale()->getLocale()) {
            $query->where(db()->raw('name REGEXP ?', ['"' . locale()->getLocale() . '":"' . e(request()->get->get('term')) . '([^"])*"']));
        } else {
            $query->where(function ($query) {
                $query
                    ->where(db()->raw('name REGEXP ?', ['"' . locale()->getLocale() . '":"' . e(request()->get->get('term')) . '([^"])*"']))
                    ->orWhere(db()->raw('name REGEXP ?', ['"' . config()->app->locale_fallback . '":"' . e(request()->get->get('term')) . '([^"])*"']));
            });
        }

        $locations = $query
            ->orderBy('_left')
            ->paginate();

        $results = [];
        
        foreach ($locations as $location) {
            $results[] = ['id' => $location->id, 'text' => $location->ancestorsAndSelfWithoutRoot()->get(['id', 'name'])->pluck('name')->implode(' &raquo; ')];
        }

        return [
            'results' => $results,
            'pagination' => [
                'more' => (boolean) (request()->get->get('page', 1) < ceil($locations->getTotal() / $locations->getLimit()) ? true : false),
            ],
        ];
    }

    public function actionSwitch($params)
    {
        if (false === $this->isAdmin()) {
            return null;
        }

        $allowed = [
            'users' => ['active', 'email_verified', 'banned'],
            'listings' => ['active'],
            'categories' => ['active', 'featured'],
            'discounts' => ['active'],
            'widgetmenuitems' => ['active', 'public', 'highlighted'],
            'products' => ['hidden', 'featured'],
            'pricings' => ['hidden'],
            'reviews' => ['active'],
            'comments' => ['active'],
            'messages' => ['active'],
            'replies' => ['active'],
            'taxes' => ['compound'],
            'emailtemplates' => ['active', 'moderatable'],
            'languages' => ['active'],
            'locations' => ['active', 'featured'],
            'page_widget' => ['active'],
            'pages' => ['active'],
            'types' => ['active'],
            'gateways' => ['active'],
            'badges' => ['active'],
        ];

        if (null !== request()->post->get('table') && array_key_exists(request()->post->get('table'), $allowed)) {
            if (null !== request()->post->get('column') && in_array(request()->post->get('column'), $allowed[request()->post->get('table')])) {
                if (null !== request()->post->get('id') && is_numeric(request()->post->get('id'))) {
                    if (null !== request()->post->get('value') && in_array(request()->post->get('value'), [0, 1])) {

                        if ('users' == request()->post->get('table') && 'active' == request()->post->get('column')) {
                            if (null !== $user = \App\Models\User::find(request()->post->get('id'))) {
                                if ('1' == request()->post->get('value')) {
                                    return $user->approve()->save();
                                } else {
                                    return $user->disapprove()->save();
                                }
                            }
                        } else if ('listings' == request()->post->get('table') && 'active' == request()->post->get('column')) {
                            if (null !== $listing = \App\Models\Listing::find(request()->post->get('id'))) {
                                if ('1' == request()->post->get('value')) {
                                    return $listing->approve()->save();
                                } else {
                                    return $listing->disapprove()->save();
                                }
                            }
                        } else if ('messages' == request()->post->get('table') && 'active' == request()->post->get('column')) {
                            if (null !== $message = \App\Models\Message::find(request()->post->get('id'))) {
                                if ('1' == request()->post->get('value')) {
                                    return $message->approve()->save();
                                } else {
                                    return $message->disapprove()->save();
                                }
                            }
                        } else if ('replies' == request()->post->get('table') && 'active' == request()->post->get('column')) {
                            if (null !== $reply = \App\Models\Reply::find(request()->post->get('id'))) {
                                if ('1' == request()->post->get('value')) {
                                    return $reply->approve()->save();
                                } else {
                                    return $reply->disapprove()->save();
                                }
                            }
                        } else if ('reviews' == request()->post->get('table') && 'active' == request()->post->get('column')) {
                            if (null !== $review = \App\Models\Review::find(request()->post->get('id'))) {
                                if ('1' == request()->post->get('value')) {
                                    $review->approve()->save();
                                } else {
                                    $review->disapprove()->save();
                                }

                                return $review->listing->recountAvgRating();
                            }
                        } else if ('comments' == request()->post->get('table') && 'active' == request()->post->get('column')) {
                            if (null !== $comment = \App\Models\Comment ::find(request()->post->get('id'))) {
                                if ('1' == request()->post->get('value')) {
                                    return $comment->approve()->save();
                                } else {
                                    return $comment->disapprove()->save();
                                }
                            }
                        } else {                        
                            return db()->table(request()->post->get('table'))
                                ->where('id', request()->post->get('id'))
                                ->update([request()->post->get('column') => (request()->post->get('value') == 1 ? 1 : null)]);
                        }
                    }
                }
            }
        }
    }

    public function actionSlugify($params)
    {
        if (null !== request()->post->get('value')) {
            if (is_array(request()->post->value)) {
                if (isset(request()->post->value[locale()->getDefault()])) {
                    return slugify(request()->post->value[locale()->getDefault()]);
                }
            } else {
                return slugify(request()->post->value);
            }
        }
    }

    public function actionHash($params)
    {
        if (false === $this->isAdmin()) {
            return null;
        }

        return bin2hex(random_bytes(16));
    }

    public function actionSort($params)
    {
        if (false === $this->isAdmin()) {
            return null;
        }

        $dragged = null;
        $related = null;

        $classes = [
            'languages' => 'Language',
            'menu' => 'MenuItem',
            'listing-field-options' => 'ListingFieldOption',
            'listing-field-constraints' => 'ListingFieldConstraint',
            'listing-fields' => 'ListingField',
            'products' => 'Product',
            'pricings' => 'Pricing',
            'types' => 'Type',
            'ratings' => 'Rating',
            'widget-fields' => 'WidgetField',
            'widget-field-options' => 'WidgetFieldOption',
            'widget-field-constraints' => 'WidgetFieldConstraint',
            'fields' => 'Field',
            'field-options' => 'FieldOption',
            'field-constraints' => 'FieldConstraint',
            'taxes' => 'Tax',
            'widget-menu' => 'WidgetMenuItem',
            'gateways' => 'Gateway',
            'themes' => 'Theme',
            'badges' => 'Badge',
        ];

        if (null !== request()->post->get('dragged')
            && is_numeric(request()->post->get('dragged'))
            && null !== request()->post->get('related')
            && is_numeric(request()->post->get('related'))
            && null !== request()->post->get('after')
            && null !== request()->post->get('source')
        ) {
            if (array_key_exists(request()->post->source, $classes)) {
                $class = '\App\Models\\' . $classes[request()->post->source];
                $model = new $class;

                $dragged = $model::find(request()->post->dragged);
                $related = $model::find(request()->post->related);
            } else if (request()->post->source == 'widgets' && null !== request()->post->get('data')) {
                if (null === $page = \App\Models\Page::find(request()->post->data)) {
                    return null;
                }

                $dragged = $page->widgets()->wherePivot('id', request()->post->dragged)->withPivot(['id', 'weight'])->first();
                $related = $page->widgets()->wherePivot('id', request()->post->related)->withPivot(['id', 'weight'])->first();

                if (null !== $dragged && null !== $related) {
                    $dragged->pivot->newQuery()
                        ->where('weight', '>', $dragged->pivot->weight)
                        ->update(db()->raw('weight = weight - 1'));

                    $related->setRelation('pivot', $related->pivot->fresh());

                    if (request()->post->after == 'true') {
                        $dragged->pivot->newQuery()
                            ->where('weight', '>', $related->pivot->weight)
                            ->update(db()->raw('weight = weight + 1'));

                        $dragged->pivot->weight = $related->pivot->weight + 1;

                    } else {
                        $dragged->pivot->newQuery()
                            ->where('weight', '>=', $related->pivot->weight)
                            ->update(db()->raw('weight = weight + 1'));

                        $dragged->pivot->weight = $related->pivot->weight;                    
                    }

                    return $dragged->pivot->save();
                }
            }
            
            if (null !== $dragged && null !== $related) {
                if (request()->post->after == 'true') {
                    $dragged->insertAfter($related);
                } else {
                    $dragged->insertBefore($related);
                }

                return $dragged->save();
            }        
        }
    }

    public function actionUpload($params)
    {
        if (false === $this->isAccount()) {
            return ['error' => __('upload.alert.permission_denied')];
        }

        if (isset($params['parameter']) 
            && '' != request()->post->get('type_id', '')
            && '' != request()->post->get('document_id', '')
        ) {

            if (null === $type = \App\Models\UploadType::find(request()->post->type_id)) {
                return ['error' => __('upload.alert.invalid_type')];
            }

            if (null === $type->public && false === $this->isAdmin()) {
                return ['error' => __('upload.alert.permission_denied')];
            }            
            
            if ($params['parameter'] == 'get') {
                $array = [];

                $files = \App\Models\File::query()
                    ->where('uploadtype_id', request()->post->type_id)
                    ->where('document_id', request()->post->document_id)
                    ->get();

                foreach ($files as $file) {
                    $array[] = [
                        'id' => $file->id,
                        'name' => $file->name . '.' . $file->extension,
                        'mime' => $file->mime,
                        'size' => $file->size,
                        'url' => $file->getUrl(),
                        'crop_data' => $file->crop_data ?? '{}',
                        'thumbnail' => (null !== $thumbnail = $file->small()) ? $thumbnail->getUrl() : '',
                    ];
                }                        

                return $array;
            } else if ($params['parameter'] == 'put') {                
                $uploadedFile = request()->files->get('file');

                if (null === $uploadedFile || false === $uploadedFile->isFile() || false === $uploadedFile->isReadable()) {
                    return ['error' => __('upload.alert.no_file')];
                }

                if ((int) $uploadedFile->getClientSize() > ($type->get('max_size') * 1024 * 1024)) {
                    return ['error' => __('upload.alert.size_limit', ['size' => locale()->formatNumber($type->get('max_size'))])];
                }

                if ($type->files()->where('document_id', request()->post->document_id)->count() >= $type->max_files) {
                    return ['error' => __('upload.alert.limit_perfield_reached', ['filename' => $uploadedFile->getClientFilename()])];
                }

                if ($type->files()->where('ip', request()->ip())->sum('size') >= config()->app->storage_size_limit_per_ip) {
                    return ['error' => __('upload.alert.size_limit_perip_reached', ['filename' => $uploadedFile->getClientFilename()])];
                }

                if ($type->files()->where('ip', request()->ip())->count() >= config()->app->storage_file_limit_per_ip) {
                    return ['error' => __('upload.alert.file_limit_perip_reached', ['filename' => $uploadedFile->getClientFilename()])];
                }

                $name = pathinfo($uploadedFile->getClientFilename());

                $fileType = $type->fileTypes()
                    ->where('mime', $uploadedFile->getMimeType())
                    ->first();

                if (null === $fileType || !array_key_exists('extension', $name) || !in_array(strtolower($name['extension']), array_map(function($extension) { return strtolower(trim($extension)); }, explode(',', $fileType->extension)))) {
                    return ['error' => __('upload.alert.invalid_mime_or_extension', ['filename' => $uploadedFile->getClientFilename()])];
                }

                $file = \App\Models\File::create([
                    'name' => slugify($name['filename']),
                    'extension' => slugify($name['extension']),
                    'uploadtype_id' => $type->id,
                    'document_id' => request()->post->document_id,
                    'mime' => $uploadedFile->getMimeType(),
                    'size' => $uploadedFile->getSize(),
                    'user_id' => auth()->user()->id,
                    'ip' => request()->ip(),
                    'version' => 1,
                ]);

                if (in_array($uploadedFile->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                    foreach (['large', 'medium', 'small'] as $size) {
                        $image = new \App\Src\Support\Image($uploadedFile);
                        $file->image_width = $image->getSourceWidth();
                        $file->image_height = $image->getSourceHeight();
                        $file->put($size . '_image_width', $type->get($size . '_image_width'));
                        $file->put($size . '_image_height', $type->get($size . '_image_height'));

                        if ($type->get($size . '_image_resize_type') == '1') {
                            $file->put($size . '_image_height', round($type->get($size . '_image_width') / $type->cropbox_width * $type->cropbox_height));
                            $image->crop($type->get($size . '_image_width'), round($type->get($size . '_image_width') / $type->cropbox_width * $type->cropbox_height));
                        } else if ($type->get($size . '_image_resize_type') == '2') {
                            $image->crop($type->get($size . '_image_width'), $type->get($size . '_image_height'));
                        } else {
                            $image->fit($type->get($size . '_image_width'), $type->get($size . '_image_height'));
                        }

                        if ('large' == $size && '' != $file->type->get('watermark_file_path')) {
                            try {
                                $watermark = new \App\Src\Http\File\File(ROOT_PATH_PROTECTED . DS . 'Storage' . DS . 'Watermarks' . DS . $file->type->get('watermark_file_path'));
                            } catch (\App\Src\Http\File\FileNotFoundException $e) {
                            }

                            $image->addWatermark(ROOT_PATH_PROTECTED . DS . 'Storage' . DS . 'Watermarks' . DS . $file->type->get('watermark_file_path'), $file->type->get('watermark_transparency'), $file->type->get('watermark_position_vertical') . ' ' . $file->type->get('watermark_position_horizontal'), 5);
                        }

                        $image->save($file->$size()->getPath(), $type->get($size . '_image_quality'));
                    }

                    $file->save();
                }
                                            
                $uploadedFile->store($file->getPath());

                return [
                    'id' => $file->id,
                    'name' => $file->name . '.' . $file->extension,
                    'mime' => $file->mime,
                    'url' => $file->getUrl(),
                    'thumbnail' => ((null !== $thumbnail = $file->small()) ? $thumbnail->getUrl() : ''),
                ];
            } else if ($params['parameter'] == 'info' && request()->post->id) {
                $file = \App\Models\File::query()
                    ->where('uploadtype_id', request()->post->type_id)
                    ->where('document_id', request()->post->document_id)
                    ->where('id', request()->post->id)
                    ->first();

                if (null !== $file) {
                    $form = form()
                        ->add('id', 'hidden', ['value' => request()->post->id])
                        ->add('title', 'text', ['label' => __('upload.form.label.title')])
                        ->add('description', 'textarea', ['label' => __('upload.form.label.description')])
                        ->setValues([
                            'title' => $file->title,
                            'description' => $file->description,
                        ]);

                    return ['html' => $form->render()];
                }
            } else if ($params['parameter'] == 'update' && isset(request()->post->id)) {
                $file = \App\Models\File::query()
                    ->where('uploadtype_id', request()->post->type_id)
                    ->where('document_id', request()->post->document_id)
                    ->where('id', request()->post->id)
                    ->first();

                if (null !== $file) {
                    $form = form()
                        ->add('id', 'hidden')
                        ->add('title', 'text', ['label' => __('upload.form.label.title')])
                        ->add('description', 'textarea', ['label' => __('upload.form.label.description')])
                        ->handleRequest('id');
    
                    $input = $form->getValues();
                    $file->title = $input->title;
                    $file->description = $input->description;
                    $file->save();

                    return true;
                }
            } else if ($params['parameter'] == 'remove' && isset(request()->post->id)) {
                $file = \App\Models\File::query()
                    ->where('uploadtype_id', request()->post->type_id)
                    ->where('document_id', request()->post->document_id)
                    ->where('id', request()->post->id)
                    ->first();
            
                if (null !== $file) {
                    return $file->delete();
                }
            } else if ($params['parameter'] == 'crop' && '' != request()->post->get('id', '') && '' != request()->post->get('data', '')) {
                $file = \App\Models\File::query()
                    ->where('uploadtype_id', request()->post->type_id)
                    ->where('document_id', request()->post->document_id)
                    ->where('id', request()->post->id)
                    ->first();

                if (null !== $file && $file->isImage()) {
                    $file->crop_data = request()->post->data;
                    $file->version = $file->version + 1;

                    if (null !== $file->get('_legacy')) {
                        $file->_legacy = null;
                    }
                    
                    $data = json_decode(request()->post->data, true);

                    foreach (['large', 'medium', 'small'] as $size) {
                        if ($file->type->get($size . '_image_resize_type') == '1') {
                            $image = new \App\Src\Support\Image($file->getPath());

                            $file->image_width = $image->getSourceWidth();
                            $file->image_height = $image->getSourceHeight();
                            $file->put($size . '_image_width', $type->get($size . '_image_width'));
                            $file->put($size . '_image_height', round($file->type->get($size . '_image_width') / $file->type->cropbox_width * $file->type->cropbox_height));

                            $image
                                ->rotate($data['rotate']*-1)
                                ->cut(
                                    $file->type->get($size . '_image_width'),
                                    round($file->type->get($size . '_image_width') / $file->type->cropbox_width * $file->type->cropbox_height),
                                    $data['x'],
                                    $data['y'],
                                    $data['width'],
                                    $data['height']
                                );

                            if ('large' == $size && '' != $file->type->get('watermark_file_path')) {
                                $image->addWatermark(ROOT_PATH_PROTECTED . DS . 'Storage' . DS . 'Watermarks' . DS . $file->type->get('watermark_file_path'), $file->type->get('watermark_transparency'), $file->type->get('watermark_position_vertical') . ' ' . $file->type->get('watermark_position_horizontal'), 5);
                            }
                            
                            $image->save($file->$size()->getPath(), $file->type->get($size . '_image_quality'));
                        }
                    }

                    $file->save();

                    return [
                        'thumbnail' => $file->small()->getUrl(),
                    ];
                }
            }
        }
    }

    public function actionMailtest($params)
    {
        if (false === $this->isAdmin()) {
            return null;
        }

        if (null !== request()->post->get('data')) {
            parse_str(request()->post->data, $formData);

            if (isset($formData['transport'])) {
                if ($formData['transport'] == 'smtp') {
                    if (isset($formData['smtp_host'])
                        && isset($formData['smtp_port'])
                        && isset($formData['smtp_encryption'])
                        && isset($formData['smtp_user'])
                        && isset($formData['smtp_password'])
                    ) {
                        $transport = (new \Swift_SmtpTransport($formData['smtp_host'], $formData['smtp_port'], $formData['smtp_encryption']))
                            ->setTimeout(5)
                            ->setUsername($formData['smtp_user'])
                            ->setPassword($formData['smtp_password']);
                    }
                } else if ($formData['transport'] == 'sendmail' && isset($formData['sendmail_command'])) {
                    $transport = new \Swift_SendmailTransport($formData['sendmail_command']);
                }

                if (isset($transport)) {
                    try {
                        $transport->start();
                        return response('OK');
                    } catch (\Swift_TransportException $e) {
                        return $e->getMessage();
                    } catch (\Exception $e) {
                        return $e->getMessage();
                    }
                }
            }            
        }

        return response('FAIL');
    }

    public function actionCascading($params)
    {
        if ('' != request()->post->get('id', '') && '' != request()->post->get('source', '') && null !== request()->post->get('value')) {
            switch (request()->post->source) {
                case 'category':
                    $model = new \App\Models\Category();
                    break;
                default:
                    $model = new \App\Models\Location();
                    break;
            }

            $array = [];

            if (request()->post->value > 0) {
                $value = request()->post->value;
            } else {
                if ($model instanceof \App\Models\Category) {
                    $value = $model->getRoot(request()->post->get('type_id') ?? 0)->id;
                } else {
                    $value = $model->getRoot()->id;
                }
            }

            if (null !== $selected = $model->where($model->getPrimaryKey(), $value)->first()) {
                $path = $selected->ancestorsAndSelf()->get();

                foreach ($path->reverse() as $node) {
                    $level = $node->getLevel();

                    if (!$node->isLeaf()) {
                        if ($model instanceof \App\Models\Category) {
                            $query = $node->children();

                            if ('1' == request()->post->get('hide_inactive', 0)) {
                                $query->whereNotNull('active');
                            }
                            
                            if ('1' == request()->post->get('hide_empty', 0)) {
                                $query->where('counter', '>', 0);
                            }

                            $children = [$node->id => __('form.category.option.select')] + $query->get()->each(function ($item, $key) { return collect(['id' => $item->id, 'name' => $item->name]); })->orderBy('name', 'asc', locale()->getLocale())->pluck('name', 'id')->all();
                        } else {
                            $children = [$node->id => __('form.location.option.select')] + $node->children()->get()->each(function ($item, $key) { return collect(['id' => $item->id, 'name' => $item->name]); })->orderBy('name', 'asc', locale()->getLocale())->pluck('name', 'id')->all();
                        }

                        $options = [
                            'options' => $children,
                            'value' => $child->id ?? 1,
                        ];

                        $array[$level] = (new \App\Src\Form\Type\Select(null, $options))->render();
                    }

                    $child = $node;
                }

                if (request()->post->source == 'location' && null !== $path->last()) {
                    $array['latitude'] = $path->last()->latitude;
                    $array['longitude'] = $path->last()->longitude;
                    $array['zoom'] = $path->last()->zoom;
                    $array['location'] = $path->pluck('name')->implode(', ');
                }

            }

            return $array;
        }
    }

    private function isAdmin()
    {
        return $this->isAccount('admin_login');
    }

    private function isUser()
    {
        return $this->isAccount('user_login');
    }

    private function isAccount($roles = null)
    {
        if (false === auth()->check($roles) 
            || ('1' != auth()->user()->id
                && (
                    false !== request()->isBanned() 
                    || null !== auth()->user()->banned 
                    || null === auth()->user()->active 
                    || null === auth()->user()->email_verified
                )
            )
        ) {
            return false;
        }

        return true;
    }

}
