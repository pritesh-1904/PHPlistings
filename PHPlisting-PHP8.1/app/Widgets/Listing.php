<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Listing
    extends \App\Src\Widget\BaseWidget
{
    protected $translatable = [
        'social_heading',
        'about_heading',
        'features_heading',
        'gallery_heading',
        'location_heading',
        'related_heading',
    ];

    public function isMultiInstance()
    {
        return true;
    }

    public function render()
    {
        $widgets = $this->getWidgetizer()->all();

        foreach ($widgets as $widget) {
            if ($widget->slug == 'listing' && $widget->getWidgetObject()->isRendered()) {
                return null;
            }
        }

        if (null !== $this->getData()->get('listing') && $this->getData()->listing instanceof \App\Models\Listing) {
            $this->rendered = true;    

            return view('widgets/listing', [
                'settings' => $this->getSettings(),
                'data' => $this->getData(),
                'logo' => $this->getLogo(),
                'related' => $this->getRelated(),
            ]);
        }
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-light',
            'social_order' => 1,
            'social_heading' => '{"en":"Social Profiles"}',
            'about_order' => 2,
            'about_heading' => '{"en":"About"}',
            'features_order' => 3,
            'features_heading' => '{"en":"Features"}',
            'gallery_order' => 4,
            'gallery_heading' => '{"en":"Gallery"}',
            'location_order' => 5,
            'location_heading' => '{"en":"Location"}',
            'related_order' => 6,
            'related_heading' => '{"en":"Related"}',
            'sharing' => 1,
            'bookmarking' => 1,
            'related_bookmarking' => 1,
            'related_sort' => 'alpha',
            'related_limit_parents' => 0,
            'related_limit_children' => 0,
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.listing.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.listing.form.label.white'),
                'bg-light' => __('widget.listing.form.label.light'),
             ]])
            ->add('social_order', 'select', ['label' => __('widget.listing.form.label.social_order'), 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'], 'constraints' => 'number'])
            ->add('social_heading', 'translatable', ['label' => __('widget.listing.form.label.social_heading')])
            ->add('about_order', 'select', ['label' => __('widget.listing.form.label.about_order'), 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'], 'constraints' => 'number'])
            ->add('about_heading', 'translatable', ['label' => __('widget.listing.form.label.about_heading')])
            ->add('features_order', 'select', ['label' => __('widget.listing.form.label.features_order'), 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'], 'constraints' => 'number'])
            ->add('features_heading', 'translatable', ['label' => __('widget.listing.form.label.features_heading')])
            ->add('gallery_order', 'select', ['label' => __('widget.listing.form.label.gallery_order'), 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'], 'constraints' => 'number'])
            ->add('gallery_heading', 'translatable', ['label' => __('widget.listing.form.label.gallery_heading')])
            ->add('location_order', 'select', ['label' => __('widget.listing.form.label.location_order'), 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'], 'constraints' => 'number'])
            ->add('location_heading', 'translatable', ['label' => __('widget.listing.form.label.location_heading')])
            ->add('related_order', 'select', ['label' => __('widget.listing.form.label.related_order'), 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'], 'constraints' => 'number'])
            ->add('related_heading', 'translatable', ['label' => __('widget.listing.form.label.related_heading')])
            ->add('sharing', 'toggle', ['label' => __('widget.listing.form.label.sharing')])
            ->add('bookmarking', 'toggle', ['label' => __('widget.listing.form.label.bookmarking')])
            ->add('related_bookmarking', 'toggle', ['label' => __('widget.listing.form.label.related_bookmarking')])
            ->add('related_sort', 'select', ['label' => __('widget.listing.form.label.related_sort'), 'options' => [
                'alpha' => __('widget.listing.form.label.related_sort_alpha'),
                'popular' => __('widget.listing.form.label.related_sort_popular'),
                'latest' => __('widget.listing.form.label.related_sort_latest'),
                'rating' => __('widget.listing.form.label.related_sort_rating'),
                'random' => __('widget.listing.form.label.related_sort_random'),
                'featured_alpha' => __('widget.listing.form.label.related_sort_featured_alpha'),
                'featured_popular' => __('widget.listing.form.label.related_sort_featured_popular'),
                'featured_latest' => __('widget.listing.form.label.related_sort_featured_latest'),
                'featured_rating' => __('widget.listing.form.label.related_sort_featured_rating'),
                'featured_random' => __('widget.listing.form.label.related_sort_featured_random'),
            ], 'constraints' => 'required'])
            ->add('related_limit_parents', 'number', ['label' => __('widget.listing.form.label.related_limit_parents'), 'constraints' => 'required'])
            ->add('related_limit_children', 'number', ['label' => __('widget.listing.form.label.related_limit_children'), 'constraints' => 'required']);
    }

    public function getLogo()
    {
        return $this->getData()->listing->logo;
    }

    public function getRelated()
    {
        $query = $this->getData()->listing
            ->parents()
            ->whereHas('type', function($query) {
                $query->whereNull('deleted');

                if (false === auth()->check('admin_login')) {
                    $query->whereNotNull('active');
                }
            })
            ->where('active', 1)
            ->where('status', 'active')
            ->with(['type', 'category', 'data']);

        switch ($this->getSettings()->related_sort) {
            case 'popular':
                $query->orderBy('impressions', 'desc');
                break;
            case 'latest':
                $query->orderBy('id', 'desc');
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
            case 'featured_rating': 
                $query->orderBy('_featured', 'desc');
                $query->orderBy('rating', 'desc');
                break;
            case 'featured_random': 
                $query->orderBy('_featured', 'desc');
                $query->orderBy(db()->raw('RAND()'));
                break;
            default: 
                $query->orderBy('title');
                break;
        }

        if ($this->getSettings()->related_limit_parents > 0) {
            $query->limit((int) $this->getSettings()->related_limit_parents);
        }

        $parents = $query->get();

        $query = $this->getData()->listing
            ->children()
            ->whereHas('type', function($query) {
                $query->whereNull('deleted');

                if (false === auth()->check('admin_login')) {
                    $query->whereNotNull('active');
                }
            })
            ->where('active', 1)
            ->where('status', 'active')
            ->with(['type', 'category', 'data']);

        switch ($this->getSettings()->related_sort) {
            case 'popular':
                $query->orderBy('impressions', 'desc');
                break;
            case 'latest':
                $query->orderBy('id', 'desc');
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
            case 'featured_rating': 
                $query->orderBy('_featured', 'desc');
                $query->orderBy('rating', 'desc');
                break;
            case 'featured_random': 
                $query->orderBy('_featured', 'desc');
                $query->orderBy(db()->raw('RAND()'));
                break;
            default: 
                $query->orderBy('title');
                break;
        }

        if ($this->getSettings()->related_limit_children > 0) {
            $query->limit((int) $this->getSettings()->related_limit_children);
        }

        $listings = $parents->merge($query->get());

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
    
        return $listings;
    }

}
