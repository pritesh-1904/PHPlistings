<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2021 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Listingsearchresultsheader
    extends \App\Src\Widget\BaseWidget
{
    protected $translatable = [
        'heading',
        'description',
    ];

    public function isMultiInstance()
    {
        return true;
    }

    public function render()
    {
        if (null === $this->getData()->get('type')) {
            return null;
        }

        $widgets = $this->getWidgetizer();

        foreach ($widgets as $widget) {
            if ($widget->slug == 'listingsearchresultsheader' && $widget->getWidgetObject()->isRendered()) {
                return null;
            }
        }

        $this->rendered = true;

        if (null !== $this->getData()->get('category')) {
            return view('widgets/listing-search-results-header-category', [
                'settings' => $this->getSettings(),
                'data' => $this->getData(),
                'children' => $this->getChildren($this->getData()->category),
            ]);
        } else if (null !== $this->getData()->get('location')) {
            return view('widgets/listing-search-results-header-location', [
                'settings' => $this->getSettings(),
                'data' => $this->getData(),
                'children' => $this->getChildren($this->getData()->location),
            ]);
        } else {
            return view('widgets/listing-search-results-header-default', [
                'settings' => $this->getSettings(),
                'data' => $this->getData(),
                'children' => $this->getChildren((new \App\Models\Category)->getRoot($this->getData()->type->id)),
            ]);
        }
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-light',
            'heading' => '{"en":"Browse By Category"}',
            'description' => '{"en":""}',
            'header' => bin2hex(random_bytes(16)),
            'show_children_category' => 1,
            'hide_empty_category' => null,
            'show_children_location' => 1,
            'sort_children_category' => 'alpha',
            'sort_children_location' => 'alpha',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.listingsearchresultsheader.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.listingsearchresultsheader.form.label.white'),
                'bg-light' => __('widget.listingsearchresultsheader.form.label.light'),
             ]])
            ->add('heading', 'translatable', ['label' => __('widget.listingsearchresultsheader.form.label.heading')])
            ->add('description', 'translatable', ['label' => __('widget.listingsearchresultsheader.form.label.description')])
            ->add('header', 'dropzone', ['label' => __('widget.listingsearchresultsheader.form.label.header'), 'upload_id' => '5'])
            ->add('show_children_category', 'toggle', ['label' => __('widget.listingsearchresultsheader.form.label.show_children_category')])
            ->add('hide_empty_category', 'toggle', ['label' => __('widget.listingsearchresultsheader.form.label.hide_empty_category')])
            ->add('show_children_location', 'toggle', ['label' => __('widget.listingsearchresultsheader.form.label.show_children_location')])
            ->add('sort_children_category', 'select', ['label' => __('widget.listingsearchresultsheader.form.label.sort_children_category'), 'options' => [
                'alpha' => __('widget.listingsearchresultsheader.form.label.sort_alpha'),
                'popular' => __('widget.listingsearchresultsheader.form.label.sort_popular'),
                'random' => __('widget.listingsearchresultsheader.form.label.sort_random'),
                'featured_alpha' => __('widget.listingsearchresultsheader.form.label.sort_featured_alpha'),
                'featured_popular' => __('widget.listingsearchresultsheader.form.label.sort_featured_popular'),
                'featured_random' => __('widget.listingsearchresultsheader.form.label.sort_featured_random'),
            ], 'constraints' => 'required'])
            ->add('sort_children_location', 'select', ['label' => __('widget.listingsearchresultsheader.form.label.sort_children_location'), 'options' => [
                'alpha' => __('widget.listingsearchresultsheader.form.label.sort_alpha'),
                'popular' => __('widget.listingsearchresultsheader.form.label.sort_popular'),
                'random' => __('widget.listingsearchresultsheader.form.label.sort_random'),
                'featured_alpha' => __('widget.listingsearchresultsheader.form.label.sort_featured_alpha'),
                'featured_popular' => __('widget.listingsearchresultsheader.form.label.sort_featured_popular'),
                'featured_random' => __('widget.listingsearchresultsheader.form.label.sort_featured_random'),
            ], 'constraints' => 'required']);
    }

    public function getChildren($model) {
        if (
            ($model instanceof \App\Models\Category && null === $this->getSettings()->show_children_category)
            || ($model instanceof \App\Models\Location && null === $this->getSettings()->show_children_location)
        ) {
            return collect();
        }
        
        $sort = ($model instanceof \App\Models\Location) ? $this->getSettings()->sort_children_location : $this->getSettings()->sort_children_category;
        
        $query = $model->children()->whereNotNull('active');

        if (null !== $this->getSettings()->hide_empty_category && $model instanceof \App\Models\Category) {
            $query->where('counter', '>', 0);
        }

        switch ($sort) {
            case 'popular': 
                $query->orderBy('impressions', 'desc');
                break;
            case 'random': 
                $query->orderBy(db()->raw('RAND()'));
                break;
            case 'featured_alpha': 
                $query->orderBy('featured', 'desc');
                $query->orderBy('name');
                break;
            case 'featured_popular': 
                $query->orderBy('featured', 'desc');
                $query->orderBy('impressions', 'desc');
                break;
            case 'featured_random': 
                $query->orderBy('featured', 'desc');
                $query->orderBy(db()->raw('RAND()'));
                break;
            default: 
                $query->orderBy('name');
                break;
        }

        $children = $query->get();

        if ('alpha' == $sort) {
            $children = $children->orderBy('name', 'asc', locale()->getLocale());
        }

        if ('featured_alpha' == $sort) {
            $children = $children->orderBy('name', 'asc', locale()->getLocale())->orderBy('featured', 'desc');
        }

        return $children;
    }

}
