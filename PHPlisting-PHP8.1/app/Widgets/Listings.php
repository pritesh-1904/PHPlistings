<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Listings
    extends \App\Src\Widget\BaseWidget
{

    protected $type = null;
    protected $translatable = [
        'caption',
        'heading',
    ];

    public function isMultiInstance()
    {
        return true;
    }

    public function render()
    {        
        if (null === $this->getType()) {
            return null;
        }

        if (null !== $this->getSettings()->get('slider')) {
            layout()->addCss('<link href="' . asset('js/swiper/css/swiper.min.css?v=844') . '" rel="stylesheet">');
            layout()->addFooterJs('<script src="' . asset('js/swiper/js/swiper.min.js?v=844') . '"></script>');
        }

        $listings = $this->getListings();

        if ($listings->count() == 0) {
            return null;
        }

        $ids = [];

        foreach ($listings as $listing) {
            $field = $listing->data->where('field_name', 'logo_id')->where('value', '!=', '')->first();
            if (null !== $field && null !== $field->active) {
                $ids[] = $field->value;
            }
        }

        if (count($ids) > 0) {
            $logos = \App\Models\File::whereIn('document_id', $ids)->where('uploadtype_id', 1)->get();
            foreach ($listings as $listing) {
                foreach ($logos as $logo) {
                    $field = $listing->data->where('field_name', 'logo_id')->first();
                    if ($logo->document_id == $field->value) {
                        $listing->setRelation('logo', $logo);
                    }
                }
            }
        }

        (new \App\Repositories\Statistics)->push('listing_search_impression', $listings->pluck('id')->all());
        
        $template = (null === $this->getSettings()->slider) ? 'widgets/listings' : 'widgets/listings-slider';

        $this->rendered = true;

        return view($template, [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
            'listings' => $listings,
            'type' => $this->getType(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-white',
            'slider' => '1',
            'slider_speed' => '2000',
            'slider_autoplay' => 1,
            'slider_autoplay_delay' => '10000',
            'caption' => '{"en":"Best Offers"}',
            'heading' => '{"en":"Featured Listings"}',
            'default_type_id' => '0',
            'featured' => '1',
            'linked' => '',
            'sort' => 'random',
            'limit' => '8',
            'bookmarking' => '1',
            'default_logo' => bin2hex(random_bytes(16)),
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.listings.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.listings.form.label.white'),
                'bg-light' => __('widget.listings.form.label.light'),
             ]])
            ->add('slider', 'toggle', ['label' => __('widget.listings.form.label.slider')])
            ->add('slider_speed', 'number', ['label' => __('widget.listings.form.label.slider_transition'), 'constraints' => 'required'])
            ->add('slider_autoplay', 'toggle', ['label' => __('widget.listings.form.label.slider_autoplay')])
            ->add('slider_autoplay_delay', 'number', ['label' => __('widget.listings.form.label.slider_autoplay_delay'), 'constraints' => 'required'])
            ->add('caption', 'translatable', ['label' => __('widget.listings.form.label.caption')])
            ->add('heading', 'translatable', ['label' => __('widget.listings.form.label.heading')])
            ->add('default_type_id', 'select', ['label' => __('widget.listings.form.label.type'), 'options' => [0 => __('widget.listings.form.label.type.auto')] + \App\Models\Type::whereNull('deleted')->get()->pluck('name_plural', 'id')->all(), 'constraints' => 'required|number'])
            ->add('featured', 'toggle', ['label' => __('widget.listings.form.label.featured')])
            ->add('linked', 'select', ['label' => __('widget.listings.form.label.linked'), 'options' => [
                '' => __('widget.listings.form.label.linked_disabled'),
                'category' => __('widget.listings.form.label.linked_category'),
                'location' => __('widget.listings.form.label.linked_location'),
                'categorylocation' => __('widget.listings.form.label.linked_categorylocation'),
                'user' => __('widget.listings.form.label.linked_user'),
            ]])
            ->add('sort', 'select', ['label' => __('widget.listings.form.label.sort'), 'options' => [
                'alpha' => __('widget.listings.form.label.sort_alpha'),
                'popular' => __('widget.listings.form.label.sort_popular'),
                'latest' => __('widget.listings.form.label.sort_latest'),
                'updated' => __('widget.listings.form.label.sort_updated'),
                'rating' => __('widget.listings.form.label.sort_rating'),
                'random' => __('widget.listings.form.label.sort_random'),
                'upcoming' => __('widget.listings.form.label.sort_upcoming'),
                'featured_alpha' => __('widget.listings.form.label.sort_featured_alpha'),
                'featured_popular' => __('widget.listings.form.label.sort_featured_popular'),
                'featured_latest' => __('widget.listings.form.label.sort_featured_latest'),
                'featured_updated' => __('widget.listings.form.label.sort_featured_updated'),
                'featured_rating' => __('widget.listings.form.label.sort_featured_rating'),
                'featured_random' => __('widget.listings.form.label.sort_featured_random'),
                'featured_upcoming' => __('widget.listings.form.label.sort_featured_upcoming'),
            ], 'constraints' => 'required'])
            ->add('limit', 'number', ['label' => __('widget.listings.form.label.limit'), 'constraints' => 'required|min:1'])
            ->add('bookmarking', 'toggle', ['label' => __('widget.listings.form.label.bookmarking')])
            ->add('default_logo', 'dropzone', ['label' => __('widget.listings.form.label.default_logo'), 'upload_id' => '1']);
    }

    public function getListings()
    {
        $query = $this->getType()->listings()->whereNotNull('active')->where('status', 'active');

        if ('Event' == $this->getType()->type) {
            $query
                ->select(db()->raw('
                (
                    SELECT event_date 
                    FROM ' . (new \App\Models\Listing())->getQuery()->getConnection()->getPrefix() . 'dates 
                    WHERE listing_id = ' . (new \App\Models\Listing())->getPrefixedTable() . '.id 
                    HAVING event_date >= CURDATE()
                    ORDER BY ABS(DATEDIFF(NOW(), event_date))
                    LIMIT 1
                ) AS event_date_upcoming'));
        }

        $query->with(['data', 'type', 'category']);

        if (null !== $this->getType() && null !== $this->getType()->localizable) {
            $query->with('location');
        }

        if (null !== $this->getSettings()->featured) {
            $query->where('_featured', 1);
        }

        $category = null;
        $location = null;

        if ('' != $this->getSettings()->get('linked', '')) {
            if (null !== $this->getData()->get('listing')) {
                $query->where('id', '!=', $this->getData()->listing->id);
                
                if (null !== $this->getData()->get('type') && $this->getType()->id == $this->getData()->listing->type_id) {
                    $category = $this->getData()->listing->category;
                }

                if (null !== $this->getType()->localizable) {
                    $location = $this->getData()->listing->location;
                }
            }

            if (null !== $this->getData()->get('category')) {
                if (null !== $this->getData()->get('type') && $this->getType()->id == $this->getData()->type->id) {
                    $category = $this->getData()->category;
                }
            }

            if (null !== $this->getData()->get('location')) {
                if (null !== $this->getType()->localizable) {
                    $location = $this->getData()->location;
                }
            }
        
            switch ($this->getSettings()->linked) {
                case 'category':
                    if (null !== $category) {
                        $query->where('category_id', $category->id);
                    }
                    break;
                case 'location':
                    if (null !== $location) {
                        $query->where('location_id', $location->id);
                    }
                    break;
                case 'categorylocation':
                    if (null !== $category) {
                        $query->where('category_id', $category->id);
                    }
                    if (null !== $location) {
                        $query->where('location_id', $location->id);
                    }
                    break;
                case 'user':
                    if (null !== $this->getData()->get('listing')) {
                        $query->where('user_id', $this->getData()->listing->user_id);
                    }
                    break;
            }
        }

        switch ($this->getSettings()->sort) {
            case 'popular':
                $query->orderBy('impressions', 'desc');
                break;
            case 'latest':
                $query->orderBy('id', 'desc');
                break;
            case 'updated':
                $query->orderBy('updated_datetime', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'random':
                $query->orderBy(db()->raw('RAND()'));
                break;
            case 'featured_alpha': 
                $query->orderBy('_featured', 'desc');
                $query->orderBy('title');
                break;
            case 'featured_popular': 
                $query->orderBy('_featured', 'desc');
                $query->orderBy('impressions', 'desc');
                break;
            case 'featured_latest': 
                $query->orderBy('_featured', 'desc');
                $query->orderBy('id', 'desc');
                break;
            case 'featured_updated': 
                $query->orderBy('_featured', 'desc');
                $query->orderBy('updated_datetime', 'desc');
                break;
            case 'featured_rating': 
                $query->orderBy('_featured', 'desc');
                $query->orderBy('rating', 'desc');
                break;
            case 'featured_random': 
                $query->orderBy('_featured', 'desc');
                $query->orderBy(db()->raw('RAND()'));
                break;
            case 'upcoming':
                if ('Event' == $this->getType()->type) {
                    $query
                        ->orderBy(db()->raw('ABS(DATEDIFF(NOW(), event_date_upcoming))'))
                        ->having('event_date_upcoming >= CURDATE()');
                    break;
                }
            case 'featured_upcoming':
                if ('Event' == $this->getType()->type) {
                    $query
                        ->orderBy('_featured', 'desc')
                        ->orderBy(db()->raw('ABS(DATEDIFF(NOW(), event_date_upcoming))'))
                        ->having('event_date_upcoming >= CURDATE()');
                    break;
                }
            default: 
                $query->orderBy('title');
                break;
        }

        $query->limit((int) $this->getSettings()->limit);

        $listings = $query->get();

        return $listings;
    }

    public function getType()
    {
        if (!$this->type instanceof \App\Models\Type) {
            if ($this->getSettings()->default_type_id == 0 && null !== $this->getData()->get('type')) {
                $this->type = $this->getData()->type;
            }

            if ($this->getSettings()->default_type_id > 0) {
                $query = \App\Models\Type::where('id', $this->getSettings()->default_type_id)->whereNull('deleted');

                if (false === auth()->check('admin_login')) {
                    $query->whereNotNull('active');
                }
                                
                if (null !== $type = $query->first()) {
                    $this->type = $type;
                }
            }
        }

        return $this->type;
    }

}
