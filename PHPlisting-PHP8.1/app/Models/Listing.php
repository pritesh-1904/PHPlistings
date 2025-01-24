<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Models;

class Listing
    extends \App\Src\Orm\Model
{

    protected $table = 'listings';
    protected $fillable = [
        'type_id',
    ];
    protected $searchable = [
        'keyword' => [['title', 'short_description', 'description'], 'fulltext', false],
        'active' => ['active', 'eq', false],
        'inactive' => ['active', 'null', false],
        'type_id' => ['type_id', 'eq', false],
        'category_id' => ['category_id', 'category', false],
        'location_id' => ['location_id', 'location', false],
        'user_id' => ['user_id', 'eq', false],
        'dates' => ['dates', 'dates', false],
    ];
    protected $sortable = [
        'newest' => ['id', 'DESC'],
        'oldest' => ['id', 'ASC'],
        'highest-rated' => ['rating', 'desc'],
        'most-popular' => ['impressions', 'desc'],

        'id' => ['id'],
        'title' => ['title'],
        'active' => ['active'],
    ];
    protected $allowed = [
        'title',
        'slug',
        'short_description',
        'description',
        'event_start_datetime',
        'event_frequency',
        'event_interval',
        'event_weekdays',
        'event_weeks',
        'event_dates',
        'event_end_datetime',
        'event_rsvp',
        'offer_start_datetime',
        'offer_end_datetime',
        'offer_price',
        'offer_discount_type',
        'offer_discount',
        'offer_count',
        'offer_terms',
        'offer_redeem',
        'address',
        'zip',
        'location_id',
        'latitude',
        'longitude',
        'zoom',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'timezone',
    ];

    public function order()
    {
        return $this->hasOne('App\Models\Order');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\Type');
    }

    public function parents()
    {
        return $this->belongsToMany(self::class, 'parent_id', 'child_id', 'listing_linked');
    }

    public function children()
    {
        return $this->belongsToMany(self::class, 'child_id', 'parent_id', 'listing_linked');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function data()
    {
        return $this->hasMany('App\Models\ListingFieldData');
    }

    public function update()
    {
        return $this->hasOne('App\Models\Update');
    }

    public function dates()
    {
        return $this->hasMany('App\Models\Date');
    }

    public function badges()
    {
        return $this->belongsToMany('App\Models\Badge');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Review');
    }

    public function messages()
    {
        return $this->hasMany('App\Models\Message');
    }

    public function claims()
    {
        return $this->hasMany('App\Models\Claim');
    }

    public function isFeatured()
    {
        return (bool) (null !== $this->_featured);
    }

    public function isNew()
    {
        $then = new \DateTime($this->added_datetime);

        return (bool) ((new \DateTime('now'))->diff($then)->days <= 5);
    }

    public function isUpdated()
    {
        if (null === $this->updated_datetime) {
            return false;
        }
        
        $then = new \DateTime($this->updated_datetime);

        return (bool) ((new \DateTime('now'))->diff($then)->days <= 5);
    }

    public function isHot()
    {
        return (bool) ($this->rating > 4);
    }

    public function isOpen($hours)
    {
        $now = new \DateTime('now', new \DateTimeZone($this->timezone));

        foreach ($hours as $record) {
            if ($now->format('N') == $record->dow) {
                $start = \DateTime::createFromFormat('H:i:s', $record->start_time, new \DateTimeZone($this->timezone));
                $end = \DateTime::createFromFormat('H:i:s', $record->end_time, new \DateTimeZone($this->timezone));
                
                if ($start <= $now && $end > $now) {
                    return true;
                }
            }
        }

        return false;
    }

    public function recountAvgRating()
    {
        $rating = $this->reviews()
            ->select(db()->expr()->avg('rating', 'average'))
            ->select(db()->expr()->count('*', 'total'))
            ->groupBy('listing_id')
            ->first(['listing_id']);

        if (null !== $rating) {
            $this->rating = round($rating->average, 1);
            $this->review_count = $rating->total;
        } else {
            $this->rating = 0;
            $this->review_count = 0;
        }

        $this->save();
    }

    public function changeUser($id)
    {
        if ($id != $this->user_id) {
            $this->user_id = $id;

            $this->order->user_id = $id;

            $this->order->save();
            
            foreach ($this->order->invoices()->get() as $invoice) {
                $invoice->changeUser($id)->save();
            }

            foreach ($this->messages as $message) {
                $message->changeRecipient($id)->save();
            }

            foreach ($this->reviews as $review) {
                $review->changeRecipient($this->user_id, $id)->save();
            }
        }

        return $this;
    }

    public function getOutputableValue($name, $schema = false)
    {        
        if ($name == '_title') {
            return $this->addSchema(
                e(\mb_strimwidth(d($this->title), 0, $this->_title_size ?? 0, '...')),
                'name',
                null,
                $schema
            );
        }

        if ($name == '_short_description') {
            return $this->addSchema(
                \mb_strimwidth(e($this->short_description), 0, $this->_short_description_size ?? 0, '...'),
                'description',
                null,
                $schema
            );
        }

        if ($name == '_description') {
            if ('' == $this->get('description', '')) {
                return '';
            }

            $dom = new \DOMDocument();
//            $dom->loadHTML(\mb_convert_encoding('<span>' . purify(d($this->get('description'))) . '</span>', 'HTML-ENTITIES', 'UTF-8'));
            $dom->loadHTML(mb_encode_numericentity('<span>' . purify(d($this->get('description'))) . '</span>', [0x80, 0x10FFFF, 0, ~0], 'UTF-8'));
            $tags = $dom->getElementsByTagName('a');

            $count = 0;
            
            for ($i = 0; $i < $tags->length; $i++) {
                $tag = $tags->item($i);

                $count++;
                
                if ($count > $this->_description_links_limit) {
                    $node = $dom->createTextNode($tag->textContent);
                    $tag->parentNode->replaceChild($node, $tag);
                    $i--;
                }
            }

            $response = purify($this->truncateHTML(purify($dom->saveHTML()), $this->get('_description_size')));

            return $this->addSchema(
                $response,
                'description',
                \strip_tags($response),
                $schema
            );
        }

        if ($name == '_category') {
            if (null !== $this->category) {
                $response = [];

                if (null === $categories = cache()->db()->get('category-ancestors.' . $this->category->id . '.' . $this->category->get('_left') . '.' . $this->category->get('_right') . '.' . locale()->getLocale())) {
                    $categories = $this->category->ancestorsAndSelfWithoutRoot()->get(['id', 'slug', 'name'])->all();
                    cache()->db()->put('category-ancestors.' . $this->category->id . '.' . $this->category->get('_left') . '.' . $this->category->get('_right') . '.' . locale()->getLocale(), $categories, 3600);
                }

                foreach ($categories as $category) {
                    $response[] = $category->name;
                }
                
                return implode(' &raquo; ', $response);
            } else {
                return '';
            }
        }

        if ($name == '_category_links') {
            if (null !== $this->category) {
                $response = [];

                if (null === $categories = cache()->db()->get('category-ancestors.' . $this->category->id . '.' . $this->category->get('_left') . '.' . $this->category->get('_right') . '.' . locale()->getLocale())) {
                    $categories = $this->category->ancestorsAndSelfWithoutRoot()->get(['id', 'slug', 'name'])->all();
                    cache()->db()->put('category-ancestors.' . $this->category->id . '.' . $this->category->get('_left') . '.' . $this->category->get('_right') . '.' . locale()->getLocale(), $categories, 3600);
                }

                foreach ($categories as $category) {
                    $response[] = '<a href="' . route($this->type->slug . '/' . $category->slug) . '">' . $category->name . '</a>';
                }

                return implode(' &raquo; ', $response);
            } else {
                return '';
            }
        }

        if ($name == '_address') {
            $format = d($this->type->get('address_format'));

            $response = (false !== $schema) ? \nl2br($format) : \nl2br(\strip_tags($format));

            if (null !== $this->get('location_id') && null !== $this->location) {
                if (null === $locations = cache()->db()->get('location-ancestors.' . $this->location->id . '.' . $this->location->get('_left') . '.' . $this->location->get('_right') . '.' . locale()->getLocale())) {
                    $locations = $this->location->ancestorsAndSelf()->get(['id', 'slug', 'name'])->all();
                    cache()->db()->put('location-ancestors.' . $this->location->id . '.' . $this->location->get('_left') . '.' . $this->location->get('_right') . '.' . locale()->getLocale(), $locations, 3600);
                }

                foreach ($locations as $key => $location) {
                    $response = preg_replace(
                        '/{location_' . $key . '}/u',
                        str_replace('$', '\$', $location->name),
                        $response
                    );
                }

                $response = preg_replace('/{location_(\d+)}/u', '', $response);
            }

            foreach ($this as $key => $value) {

                if ($key == 'location_id') {
                    continue;
                }
                
                if (strstr($response, '{' . $key . '}')) {
                    $response = preg_replace('/{' . $key . '}/u', str_replace('$', '\$', $this->get($key)), $response);
                }
            }

            return $response;
        }
        
        if ($name == '_rating') {
            return view('misc/rating', [
                'rating' => $this->rating,
            ]);
        }
    }

    public function getOutputableValueWithSchema($name)
    {
        return $this->getOutputableValue($name, true);
    }

    public function addSchema($value, $itemprop, $content = null, $enabled = true) {
        if (false !== $enabled) {
            return (null !== $itemprop && '' !== $itemprop ? '<span itemprop="' . e($itemprop) . '"' . (null !== $content ? ' content="' . e($content) . '"' : '') . '>' . $value . '</span>' : $value);
        }

        return $value;
    }

    public function getOutputableForm($search = false)
    {
        $name = 'listings-' . $this->type_id . '-' . locale()->getLocale() . '-' . (false === $search ? 'outputable' : 'outputable-search-result') . '-form';

        if (!cache()->collection()->has($name)) {
            $fields = \App\Models\ListingFieldGroup::find(1)
                ->fields()
                ->where((false === $search ? 'outputable' : 'outputable_search'), 1)
                ->where('type_id', $this->type_id)
                ->whereNotNull('customizable')
                ->with('options')
                ->orderBy('weight')
                ->get();

            cache()->collection()->put($name, 
                form()->importWithoutConstraints($fields)
            );
        }

        return cache()->collection()->get($name);
    }

    public function getOutputableSearchResultForm()
    {
        return $this->getOutputableForm(true);
    }

    public function getAllFormFields()
    {
        if (!cache()->collection()->has('listings-' . $this->type_id . '-' . locale()->getLocale() . '-form-fields')) {
            cache()->collection()->put('listings-' . $this->type_id . '-' . locale()->getLocale() . '-form-fields', 
                (new \App\Models\ListingField())
                    ->where('listingfieldgroup_id', 1)
                    ->where('type_id', $this->type_id)
                    ->with('options', function ($query) {
                        $query->orderBy('weight');
                    })
                    ->get()
                );
        }

        return cache()->collection()->get('listings-' . $this->type_id . '-' . locale()->getLocale() . '-form-fields');
    }

    public function getFormFields($type = 'submit', $options = [])
    {
        $query = (new \App\Models\ListingField())
            ->where('listingfieldgroup_id', 1)
            ->where('type_id', $this->type_id);

        if ($type == 'search') {
            $query
                ->where('queryable', 1)
                ->whereNotIn('type', [
                    'captcha',
                    'color',
                    'dates',
                    'dropzone',
                    'hidden',
                    'hours',
                    'password',
                    'ro',
                    'separator',
                    'youtube',
                ]);
        } else if ($type == 'submit') {
            $query->where('submittable', 1);
        } else if ($type == 'update') {
            $query->where('updatable', 1);
        }

        if (null !== $this->category_id || false !== array_key_exists('pricing_id', $options) || $type == 'search') {
            $query->where(function ($query) use ($options, $type) {
                $query->where(function ($query) use ($options, $type) {
                    if (null !== $this->category_id) {
                        $query->whereHas('categories', function ($relation) {
                            $relation->where('category_id', $this->category_id);
                        });                    
                    }

                    if (false !== array_key_exists('pricing_id', $options)) {
                        $product = \App\Models\Pricing::find($options['pricing_id'])->product;

                        $query->whereHas('products', function ($relation) use ($options, $product) {
                            $relation->where('product_id', $product->id);
                        });

                        if ('search' != $type && null != $product->get('_backlink')) {
                            $query->orWhere('name', 'website');
                        }
                    }

                    $query->orWhereNull('customizable');
                });
            });
        }

        return $query
            ->orderBy('weight')
            ->with([
                'options',
                'constraints',
            ])
            ->get();
    }

    public function getTree($typeId, $userId = null)
    {
        $tree = [];

        $query = $this->getQuery();

        if (null !== $userId) {
            $query = \App\Models\User::find($userId);

            if (null !== $query) {
                $query = $query->listings();
            } else {
                return $tree;
            }
        }

        foreach ($query->where('type_id', $typeId)->get() as $listing) {
            $tree[] = ['key' => $listing->id, 'title' => $listing->title];
        }

        return $tree;
    }

    public function getEventDates()
    {
        $start = new \DateTime($this->event_start_datetime);
        $end = new \DateTime($this->event_end_datetime);

        $start->setTime(0,0,0);
        $end->setTime(23,59,59);

        $dates = [];

        switch ($this->event_frequency) {
            case 'once':
                $period = new \DatePeriod(
                    $start,
                    new \DateInterval('P1D'),
                    $end);

                foreach ($period as $date) {
                    $dates[] = $date->format('Y-m-d');
                }

                break;
            case 'daily':
                $period = new \DatePeriod(
                    $start,
                    new \DateInterval('P' . $this->event_interval . 'D'),
                    $end);

                foreach ($period as $date) {
                    $dates[] = $date->format('Y-m-d');
                }

                break;
            case 'weekly':
                if (null === $this->event_weekdays) {                    
                    $period = new \DatePeriod(
                        $start,
                        new \DateInterval('P' . $this->event_interval . 'W'),
                        $end);

                    foreach ($period as $key => $date) {
                        $dates[] = $date->format('Y-m-d');
                    }
                } else {
                    $period = new \DatePeriod(
                        $start,
                        new \DateInterval('P1D'),
                        $end);

                    $skip = 0;
                    
                    foreach ($period as $key => $date) {
                        if ($skip > 0) {
                            $skip--;
                            continue;
                        }

                        if (in_array($date->format('N'), explode(',', $this->event_weekdays))) {
                            $dates[] = $date->format('Y-m-d');
                        }

                        if (count($dates) > 0 && $date->format('N') == 7 && $this->event_interval > 1) {
                            $skip = ($this->event_interval - 1) * 7;
                        }
                    }
                }

                break;
            case 'monthly':
                if (null === $this->event_weekdays && null === $this->event_weeks) {
                    $period = new \DatePeriod(
                        $start,
                        new \DateInterval('P' . $this->event_interval . 'M'),
                        $end);

                    foreach ($period as $key => $date) {
                        $dates[] = $date->format('Y-m-d');
                    }
                } else if (null !== $this->event_weekdays && null === $this->event_weeks) {
                    $period = new \DatePeriod(
                        $start,
                        new \DateInterval('P1D'),
                        $end);

                    $skip = 0;
                    
                    foreach ($period as $key => $date) {
                        if ($skip > 0) {
                            $skip--;
                            continue;
                        }

                        if (in_array($date->format('N'), explode(',', $this->event_weekdays))) {
                            $dates[] = $date->format('Y-m-d');
                        }

                        $lastDayOfMonth = (clone ($date))->modify('last day of this month');

                        if (count($dates) > 0 && $date->format('d') == $lastDayOfMonth->format('d') && $this->event_interval > 1) {
                            $skipToDate = (new \DateTime($date->format('Y-m-d')))->modify('+' . ($this->event_interval - 1) . ' month');
                            $skip = $skipToDate->diff($date)->format('%a');
                        }
                    }
                } else if (null !== $this->event_weekdays && null !== $this->event_weeks) {
                    $period = new \DatePeriod(
                        $start,
                        new \DateInterval('P1D'),
                        $end);

                    $skip = 0;
                    
                    foreach ($period as $key => $date) {
                        if ($skip > 0) {
                            $skip--;
                            continue;
                        }

                        $firstDayOfMonth = new \DateTime($date->format('Y-m-1'));

                        if (in_array($date->format('N'), explode(',', $this->event_weekdays)) 
                            && in_array(ceil(($firstDayOfMonth->format('N') + $date->format('j') - 1) / 7), explode(',', $this->event_weeks)))
                        {
                            $dates[] = $date->format('Y-m-d');
                        }

                        $lastDayOfMonth = (clone $date)->modify('last day of this month');

                        if (count($dates) > 0 && $date->format('d') == $lastDayOfMonth->format('d') && $this->event_interval > 1) {
                            $skipToDate = (new \DateTime($date->format('Y-m-d')))->modify('+' . ($this->event_interval - 1) . ' month');
                            $skip = $skipToDate->diff($date)->format('%a');
                        }
                    }
                }

                break;
            case 'yearly':
                $period = new \DatePeriod(
                    $start,
                    new \DateInterval('P' . $this->event_interval. 'Y'),
                    $end);

                foreach ($period as $date) {
                    $dates[] = $date->format('Y-m-d');
                }

                break;
            case 'custom':
                $dates = array_map('trim', explode(',', $this->event_dates));

                break;
            default:
                break;
        }

        return $dates;
    }

    public function saveEventDates(array $dates)
    {
        $this->dates()->delete();

        $array = [];

        foreach ($dates as $date) {
            $array[] = [
                'listing_id' => $this->id,
                'event_date' => $date,
            ];
        }

        if (count($array) > 0) {
            return db()->table('dates')->insert($array);
        }

        return true;
    }

    public function getEventUpcomingDate(\App\Src\Orm\Collection $dates = null) {
        if (null === $dates) {
            $dates = $this->dates()->get();
        }

        $dates = $dates->pluck('event_date')->all();

        if (in_array(date('Y-m-d'), $dates)) {
            return date('Y-m-d');
        }

        array_push($dates, date('Y-m-d'));

        sort($dates);

        $key = array_search(date('Y-m-d'), $dates);

        if (false !== $date = array_slice($dates, $key + 1, 1)) {
            if (isset($date[0])) {
                return $date[0];
            }
        }

            return null;
    }

    public function getOfferPriceWithDiscount()
    {
        if ('Offer' == $this->type->type) {
            switch ($this->get('offer_discount_type')) {
                case 'percentage':
                    $price = (float) $this->offer_price - ((float) $this->offer_price / 100 * $this->offer_discount);
                    break;
                case 'fixed':
                    $price = (float) $this->offer_price - (float) $this->offer_discount;
                    break;
                default:
                    return null;
            }

            if ($price < 0) {
                $price = '0';
            }

            return $price;
        }

        return null;
    }

    public function getOfferDiscountDescription()
    {
        if ('Offer' == $this->type->type) {
            switch ($this->get('offer_discount_type')) {
                case 'percentage':
                    return __('listing.label.discount_percentage', ['value' => $this->get('offer_discount')]);
                    break;
                case 'fixed':
                    return __('listing.label.discount_fixed', ['value' => locale()->formatPrice($this->get('offer_discount'))]);
                    break;
            }
        }

        return null;
    }

    public function synchronizeProduct(\App\Models\Product $product = null)
    {
        if (null === $product) {
            $product = $this->order->pricing->product;
        }

        foreach ($this as $option => $value) {
            if (substr($option, 0, 1) === '_') {
                $this->put($option, null);
            }            
        }

        foreach ($product as $option => $value) {
            if (
                in_array($option, [
                    '_featured',
                    '_page',
                    '_position',
                    '_extra_categories',
                    '_title_size',
                    '_short_description_size',
                    '_description_size',
                    '_description_links_limit',
                    '_gallery_size',
                    '_address',
                    '_map',
                    '_event_dates',
                    '_send_message',
                    '_reviews',
                    '_seo',
                    '_backlink',
                    '_dofollow',
                ])
            ) {
                $this->put($option, $value);
            }
        }

        $data = [];

        foreach ($this->getAllFormFields() as $field) {
            if (null !== $field->customizable) {
                $existing = $this->data->where('field_name', $field->name)->first();

                $active = null;
                
                if (null !== $product->fields->where('listingfieldgroup_id', 1)->where('name', $field->name)->first() && $this->category->fields->contains('name', $field->name)) {
                    $active = 1;
                }
                
                $data[] = [
                    'active' => $active,
                    'listing_id' => $this->id,
                    'field_name' => $field->name,
                    'value' => (null !== $existing ? $existing->value : null),
                ];
            }
        }

        $this->data()->delete();

        if (count($data) > 0) {
            db()->table('listingfielddata')->insert($data);
        }

        if ($this->categories->count() > $this->_extra_categories) {
            $this->categories()->sync($this->categories->slice(0, $this->_extra_categories)->pluck('id')->all());
        }
        
        if ('Event' == $this->type->type && $this->dates->count() > $this->_event_dates) {
            $dates = [];
            
            foreach ($this->dates->slice(0, $this->_event_dates) as $date) {
                $dates[] = [
                    'listing_id' => $date->listing_id,
                    'event_date' => $date->event_date,
                ];
            }

            db()->table('dates')->where('listing_id', $this->id)->delete();

            db()->table('dates')->insert($dates);
        }

        $this->badges()->detach(null, function ($query) { $query->whereNotNull('product'); });

        if ($product->badges->count() > 0) {
            foreach ($product->badges as $badge) {
                if (null === $this->badges->where('id', $badge->id)->first()) {
                    $this->badges()->attach($badge->id, ['product' => 1]);
                }
            }
        }

        $this->sync_product = null;

        $this->deadlinkchecker_datetime = null;
        $this->deadlinkchecker_retry = null;

        $this->backlinkchecker_datetime = null;
        $this->backlinkchecker_retry = null;
        
        return $this;
    }

    public function saveWithData(\App\Src\Support\Collection $input)
    {
        $data = [];
        $exists = $this->exists;

        $result = $this->save();

        foreach ($this->getAllFormFields() as $field) {
            if (null !== $field->customizable) {
                $existing = (false !== $exists) ? $this->data->where('field_name', $field->name)->first() : null;
                $value = (false === $input->has($field->name) ? (null !== $existing ? $existing->value : null) : $input->get($field->name));

                if (in_array($field->type, ['dropzone', 'hours']) && '' == $value) {
                    $value = bin2hex(random_bytes(16));
                }

                $data[] = [
                    'active' => (null !== $existing ? $existing->active : null),
                    'listing_id' => $this->id,
                    'field_name' => $field->name,
                    'value' => $value,
                ];
            }
        }

        $this->data()->delete();

        if (count($data) > 0) {
            db()->table('listingfielddata')->insert($data);
        }
        
        return $result;
    }
    
    public function delete($id = null)
    {
        $this->order->deactivate();

        foreach ($this->getAllFormFields() as $field) {
            if (in_array($field->type, ['dropzone', 'hours'])) {
                if (null === $field->customizable) {
                    $value = $this->get($field->name);
                } else if (null !== $this->data()->where('field_name', $field->name)->first()) {
                    $value = $this->data()->where('field_name', $field->name)->first()->value;
                } else {
                    $value = null;
                }

                if (null !== $value && '' != $value) {
                    if ('dropzone' == $field->type) {
                        if (null !== $files = \App\Models\File::where('document_id', $value)->get()) {
                            foreach ($files as $file) {
                                $file->delete();
                            }
                        }
                    } else if ('hours' == $field->type) {
                        if (null !== $hours = \App\Models\Hour::where('hash', $value)->get()) {
                            foreach ($hours as $hour) {
                                $hour->delete();
                            }
                        }
                    }
                }
            }
        }

        $this->data()->delete();
        $this->parents()->detach();
        $this->children()->detach();
        
        foreach ($this->messages as $message) {
            $message->delete();
        }

        foreach ($this->reviews as $review) {
            $review->delete();
        }

        $this->update()->delete();
        $this->dates()->delete();
        $this->order->delete();

        $this->badges()->detach();
        $this->categories()->detach();
        $this->claims()->delete();

        db()->table('stats')
            ->where( function ($query) {
                $query
                    ->where('type', 'listing_impression')
                    ->orWhere('type', 'listing_search_impression')
                    ->orWhere('type', 'listing_phone_view')
                    ->orWhere('type', 'listing_website_click');
            })
            ->where('type_id', $this->id)
            ->delete();
        
        db()->table('rawstats')
            ->where( function ($query) {
                $query
                    ->where('type', 'listing_impression')
                    ->orWhere('type', 'listing_search_impression')
                    ->orWhere('type', 'listing_phone_view')
                    ->orWhere('type', 'listing_website_click');
            })
            ->where('type_id', $this->id)
            ->delete();

        db()->table('bookmarks')
            ->where('listing_id', $this->id)
            ->delete();

        return parent::delete($id);
    }

    public function setSearchable($attribute, $type, $external = false)
    {
        if (!array_key_exists($attribute, $this->searchable)) {
            $this->searchable[$attribute] = [$attribute, $type, $external];
        }

        return $this;        
    }

    public static function search(\App\Src\Orm\Model $bindModel = null, array $options = [], $identifier = null)
    {
        if (null !== $bindModel) {
            $query = $bindModel->getQuery();
        } else {
            $query = (new static())->newQuery();
        }

        $identifier = $identifier ?? getRoute();

        $prefixedTable = $query->getModel()->getPrefixedTable();

        if (session()->has($identifier)) {
            session()->forget($identifier);
        }

        if (request()->get->count() > 0) {
            session()->put($identifier, request()->get->all());
        }

        $fields = $query->getModel()->getFormFields('search');

        foreach ($fields as $field) {
            if ($field->queryable) {
                if (in_array($field->type, [
                    'captcha',
                    'color',
                    'dropzone',
                    'hidden',
                    'hours',
                    'password',
                    'ro',
                    'separator',
                    'youtube',
                ])) {
                    continue;
                }
                
                if (in_array($field->type, [
                    'date',
                    'dates',
                    'datetime',
                    'time',
                ])) {
                    $field->search_type = $field->type;
                }

                if ($field->search_type == 'range' && false === in_array($field->type, ['number', 'price'])) {
                    $field->search_type = 'eq';
                }

                if (false !== in_array($field->type, ['checkbox', 'mselect'])) {
                    $field->search_type = ($field->search_type == 'eq') ? 'all' : 'any';
                }
                
                if ($field->type == 'select') {
                    $field->search_type = 'eq';
                }

                if ($field->type == 'keywords') {
                    $field->search_type = 'keywords';
                }

                $query->getModel()->setSearchable($field->name, $field->search_type, (null !== $field->customizable));
            }

            if ($field->sortable) {
                $query->getModel()->setSortable($field->name, $field->name);
            }
        }

        foreach (request()->get as $parameter => $value) {
            if ('' === $value) {
                continue;
            }                       
  
            if (array_key_exists($parameter, $query->getModel()->searchable)) {
                list($column, $expression, $external) = $query->getModel()->searchable[$parameter];

                    if (false !== $external) {
                        $query->leftJoin(
                            db()->getPrefix() . 'listingfielddata',
                            db()->raw($prefixedTable . '.id = ' . $column . '.listing_id AND ' . $column . '.active IS NOT NULL AND ' . $column . '.field_name = ?', [$column]),
                            $column
                        );
                    }

                switch ($expression) {
                    case 'category':
                        if (null !== $category = \App\Models\Category::find($value)) {
                            $query->where(function ($query) use ($category, $prefixedTable) {
                                $query
                                    ->whereIn($prefixedTable . '.category_id', $category->descendantsAndSelf()->get()->pluck('id')->all())
                                    ->orWhereHas('categories', function ($relation) use ($category, $prefixedTable) {
                                        $relation->where('category_id', $category->id);
                                    });
                            });
                        }

                        break;
                    case 'location':
                        if (isset(request()->get->radius) && is_numeric(request()->get->radius) && request()->get->radius > 0 && request()->get->radius <= 100) {
                            if (null !== $location = \App\Models\Location::find($value)) {
                                $query
                                    ->select(db()->raw('(
                                        ' . (isset($options['distance_type']) && $options['distance_type'] == 'kilometers' ? '6371' : '3959') . ' * acos (
                                            cos ( radians(?) )
                                            * cos ( radians (' . $prefixedTable . '.latitude) )
                                            * cos ( radians (' . $prefixedTable . '.longitude) - radians (?) )
                                            + sin ( radians (?) )
                                            * sin ( radians (' . $prefixedTable . '.latitude) )
                                            )
                                        ) AS _distance
                                    ', [
                                        $location->latitude,
                                        $location->longitude,
                                        $location->latitude
                                    ]))
                                    ->having(
                                        db()->raw(
                                            '_distance <= ?',
                                            [(int) request()->get->radius]
                                        )
                                    );
            
                                $query->getModel()->setSortable('distance', '_distance');
                            }
                        } else {
                            $location = \App\Models\Location::find($value);
                            if (null !== $location) {
                                $query->whereIn($prefixedTable . '.location_id', $location->descendantsAndSelf()->get()->pluck('id')->all());
                            }
                        }

                        break;
                    case 'fulltext':
                        if (is_array($column)) {
                            $column = array_map(function ($value) use ($prefixedTable) { return $prefixedTable . '.' . $value; }, $column);
                        } else {
                            $column = $prefixedTable . '.' . $column;
                        }

                        $query
                            ->select(
                                db()->raw(
                                    'MATCH(' . implode(', ', (array) $column) . ') AGAINST(?) AS _score',
                                    [$value]
                                )
                            )
                            ->where(
                                db()->raw(
                                    '(MATCH(' . implode(', ', (array) $column) . ') AGAINST(?)) > 0.1',
                                    [$value]
                                )
                            );

                        $query->getModel()->setSortable('relevance', '_score');

                        break;
                    case 'null':
                        if (false === $external) {
                            $query->whereNull($prefixedTable . '.' . $column);
                        } else {
                            $query->where(function($query) use ($column) {
                                $query
                                    ->whereNull($column . '.value');
                            });
                        }

                        break;
                    case 'like':
                        $value = '%' . $value . '%';
                    case 'eq':
                        if (false === $external) {
                            if (null === $value) {
                                $query->whereNull($prefixedTable . '.' . $column);
                            } else {
                                $query->where($prefixedTable . '.' . $column, $expression, $value);
                            }
                        } else {
                            $query->where(function($query) use ($column, $expression, $value) {
                                if (null === $value) {
                                    $query->whereNull($column . '.value');
                                } else { 
                                    $query->where($column . '.value', $expression, $value);
                                }
                            });
                        }

                        break;
                    case 'keywords':
                        $value = array_map('trim', explode(',', $value));
                    case 'all':
                        $query->where(
                            function ($query) use ($value, $column, $external, $prefixedTable) {
                                if (false === $external) {
                                    foreach ($value as $item) {
                                        $query->where(db()->raw('FIND_IN_SET(?, ' . $prefixedTable . '.' . $column . ') > 0', [$item]));
                                    }
                                } else {
                                    $query
                                        ->where(function ($query) use ($value, $column) {
                                            foreach ($value as $item) {
                                                $query->where(db()->raw('FIND_IN_SET(?, ' . $column . '.value) > 0', [$item]));
                                            }
                                        });
                                }
                            });

                        break;
                    case 'any':
                        $query->where(
                            function ($query) use ($value, $column, $external, $prefixedTable) {
                                if (false === $external) {
                                    foreach ($value as $item) {
                                        $query->orWhere(db()->raw('FIND_IN_SET(?, ' . $prefixedTable . '.' . $column . ') > 0', [$item]));
                                    }
                                } else {
                                    $query
                                        ->where(function ($query) use ($column, $value) {
                                            foreach ($value as $item) {
                                                $query->orWhere(db()->raw('FIND_IN_SET(?, ' . $column . '.value) > 0', [$item]));
                                            }
                                        });
                                }
                            });

                        break;
                    case 'range':
                        if (strstr($value, ';')) {
                            if (count(explode(';', $value)) == 2) {
                                list($min, $max) = explode(';', $value);
                                if (false === $external) {
                                    $query->where($query->raw('(CAST(' . $prefixedTable . '.' . $column . ' AS SIGNED INTEGER) >= ? AND CAST(' . $prefixedTable . '.' . $column . ' AS SIGNED INTEGER) <= ?)', [$min, $max]));
                                } else {
                                    $query->where(function ($query) use ($min, $max, $column) {
                                        $query
                                            ->where(db()->raw('(CAST(' . $column . '.value AS SIGNED INTEGER) >= ? AND CAST(' . $column . '.value AS SIGNED INTEGER) <= ?)', [$min, $max]));
                                    });
                                }
                            }
                        }

                        break;
                    case 'date':
                        if (null !== $value && '' !== $value) {
                            try {
                                $value = (new \App\Src\DataTransformer\LocalizedStringToDate(locale()->getDateFormat()))->transform($value);
                            } catch (\App\DataTranformer\FailedTransformationException $e) {
                                $value = '';
                            }

                            $query->where($column . (false !== $external ? '.value' : ''), $value);
                        }

                        break;
                    case 'dates':
                        if (null !== $value && '' !== $value) {
                            try {
                                $value = (new \App\Src\DataTransformer\LocalizedStringToDates(locale()->getDateFormat()))->transform($value);
                                $dates = array_map('trim', explode(',', $value));
                            } catch (\App\DataTranformer\FailedTransformationException $e) {
                                $dates = [date('Y-m-d')];
                            }

                            $query->whereIn('dates.event_date', $dates);
                        }

                        break;
                    case 'datetime':
                        if (null !== $value && '' !== $value) {
                            try {
                                $value = (new \App\Src\DataTransformer\LocalizedStringToDatetime(locale()->getDateFormat() . ' ' . locale()->getTimeFormat()))->transform($value);
                            } catch (\App\DataTranformer\FailedTransformationException $e) {
                                $value = '';
                            }

                            $query->where($column . (false !== $external ? '.value' : ''), $value);
                        }

                        break;
                    case 'time':
                        if (null !== $value && '' !== $value) {
                            try {
                                $value = (new \App\Src\DataTransformer\LocalizedStringToTime(locale()->getTimeFormat()))->transform($value);
                            } catch (\App\DataTranformer\FailedTransformationException $e) {
                                $value = '';
                            }

                            $query->where($column . (false !== $external ? '.value' : ''), $value);
                        }

                        break;
                }
            }
        }

        if (null !== request()->get->get('sort') && '' != request()->get->get('sort') && isset($query->getModel()->sortable[request()->get->get('sort')][0])) {
            $column = $query->getModel()->sortable[request()->get->sort][0];

            $direction = 'asc';

            if (null !== request()->get->get('sort_direction') && in_array(request()->get->get('sort_direction'), ['asc', 'desc'])) {
                $direction = request()->get->get('sort_direction');
            }

            if (isset($query->getModel()->sortable[request()->get->sort][1])) {
                $direction = $query->getModel()->sortable[request()->get->sort][1];
            }

//            $query->orderBy($prefixedTable . '.' . $column, $direction);
            $query->orderBy($column, $direction);
        }

        return $query;
    }

    public function approve()
    {
        $this->active = 1;

        if (null !== $this->type->get('active')) {
            (new \App\Repositories\EmailQueue())->push(
                'user_listing_approved',
                $this->user->id,
                [
                    'id' => $this->user->id,
                    'first_name' => $this->user->first_name,
                    'last_name' => $this->user->last_name,
                    'email' => $this->user->email,

                    'listing_id' => $this->id,
                    'listing_title' => $this->title,
                    'listing_type_singular' => $this->type->name_singular,
                    'listing_type_plural' => $this->type->name_plural,
                ],
                [$this->user->email => $this->user->getName()],
                [config()->email->from_email => config()->email->from_name]
            );
        }

        return $this;
    }

    public function disapprove()
    {
        $this->active = null;

        return $this;
    }

    private function truncateHTML($string, $length)
    {
        if (false === preg_match('/<\s*(pre|plaintext)/', $string) && mb_strlen(preg_replace('/<.*?>/', '', $string), 'UTF-8') <= $length) {
            return $string;
        }

        preg_match_all('/(<.+?>)?([^<>]*)/s', $string, $matches, \PREG_SET_ORDER);

        $totalLength = 0;
        $openTags = [];
        $plainMode = false;
        $plainTag = false;
        $result = '';

        foreach ($matches as $match) {
            if (false === empty($match[1])) {
                if (preg_match('/^<(\s*.+?\/\s*|\s*(area|base|br|col|embed|hr|img|input|keygen|link|meta|param|source|track|wbr)(\s.+?)?)>$/is', $match[1])){

                } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $match[1], $tagMatches)) {
                    $tag = false;

                    if (strtolower($tagMatches[1]) == $plainMode) {
                        $plainMode = false;
                    } else {
                        $pos = array_search($tagMatches[1], $openTags);

                        if (false !== $pos) {
                            unset($openTags[$pos]);
                        }
                    }
                } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $match[1], $tagMatches)) {
                    $tag = strtolower($tagMatches[1]);

                    $plainTag = (false !== in_array($tag, array('pre','plaintext'))) ? $tag : false;

                    if (false === $plainMode && false === $plainTag) {
                        array_unshift($openTags, mb_strtolower($tag));
                    }
                }

                if (false === $plainMode) {
                    $result .= $match[1];
                }
            }

            $left = $length - $totalLength;

            if (false !== $plainMode || ($plainTag && $tag)) {
                $content = $plainMode ? $match[0] : $match[2];

                if (mb_strlen($content) <= $left) {
                    $result .= $content;
                    $totalLength += mb_strlen($content);
                } else {
                    $result .= mb_substr($content, 0, $left);
                    $totalLength += $left;
                }

                if (false !== $plainTag && false === $plainMode) {
                    $plainMode = $plainTag;
                }
            } else {
                $contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};|[\r\n\s]{2,}/i', ' ', $match[2]));

                if ($totalLength + $contentLength > $length) {

                    $entitiesLength = 0;

                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};|[\r\n\s]{2,}/i', $match[2], $entities, \PREG_OFFSET_CAPTURE)) {

                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entitiesLength <= $left) {
                                $left--;
                                $entitiesLength += mb_strlen($entity[0]);
                            } else {
                                break;
                            }
                        }
                    }

                    $result .= mb_substr($match[2], 0, $left + $entitiesLength);

                    break;
                } else {
                    $result .= $match[2];

                    $totalLength += $contentLength;
                }
            }

            if ($totalLength >= $length) {
                break;
            }
        }

        foreach ($openTags as $tag) {
            $result .= '</'.$tag.'>';
        }

        return $result;
    }

}
