<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Locations
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

        if (null === $this->getType()->localizable) {
            return null;
        }

        if (null !== $this->getSettings()->get('slider')) {
            layout()->addCss('<link href="' . asset('js/swiper/css/swiper.min.css?v=844') . '" rel="stylesheet">');
            layout()->addFooterJs('<script src="' . asset('js/swiper/js/swiper.min.js?v=844') . '"></script>');
        }

        $locations = $this->getLocations();

        if ($locations->count() == 0) {
            return null;
        }

        $template = (null === $this->getSettings()->slider) ? 'widgets/locations' : 'widgets/locations-slider';

        $this->rendered = true;

        return view($template, [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
            'locations' => $locations,
            'type' => $this->getType(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-white',
            'slider' => null,
            'slider_speed' => '2000',
            'slider_autoplay' => 1,
            'slider_autoplay_delay' => '10000',
            'caption' => '{"en":"Browse"}',
            'heading' => '{"en":"Locations"}',
            'default_type_id' => '0',
            'root' => (new \App\Models\Location)->getRoot()->id,
            'featured' => null,
            'show_description' => '1',
            'sort' => 'alpha',
            'limit' => '12',
            'default_logo' => bin2hex(random_bytes(16)),
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.locations.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.locations.form.label.white'),
                'bg-light' => __('widget.locations.form.label.light'),
             ]])
            ->add('slider', 'toggle', ['label' => __('widget.locations.form.label.slider')])
            ->add('slider_speed', 'number', ['label' => __('widget.locations.form.label.slider_transition'), 'constraints' => 'required'])
            ->add('slider_autoplay', 'toggle', ['label' => __('widget.locations.form.label.slider_autoplay')])
            ->add('slider_autoplay_delay', 'number', ['label' => __('widget.locations.form.label.slider_autoplay_delay'), 'constraints' => 'required'])
            ->add('caption', 'translatable', ['label' => __('widget.locations.form.label.caption')])
            ->add('heading', 'translatable', ['label' => __('widget.locations.form.label.heading')])
            ->add('default_type_id', 'select', ['label' => __('widget.locations.form.label.type'), 'options' => [0 => __('widget.locations.form.label.type.auto')] + \App\Models\Type::whereNull('deleted')->get()->pluck('name_plural', 'id')->all(), 'constraints' => 'required|number'])
            ->add('root', 'cascading', ['label' => __('widget.locations.form.label.root'), 'cascading_source' => 'location', 'constraints' => 'required|number'])
            ->add('featured', 'toggle', ['label' => __('widget.locations.form.label.featured_only')])
            ->add('show_description', 'toggle', ['label' => __('widget.locations.form.label.show_description')])
            ->add('sort', 'select', ['label' => __('widget.locations.form.label.sort'), 'options' => [
                'alpha' => __('widget.locations.form.label.sort_alpha'),
                'popular' => __('widget.locations.form.label.sort_popular'),
                'random' => __('widget.locations.form.label.sort_random'),
                'featured_alpha' => __('widget.locations.form.label.sort_featured_alpha'),
                'featured_popular' => __('widget.locations.form.label.sort_featured_popular'),
                'featured_random' => __('widget.locations.form.label.sort_featured_random'),
            ], 'constraints' => 'required'])
            ->add('limit', 'number', ['label' => __('widget.locations.form.label.limit'), 'constraints' => 'required'])
            ->add('default_logo', 'dropzone', ['label' => __('widget.locations.form.label.default_logo'), 'upload_id' => '4']);
    }

    public function getLocations()
    {
        $query = \App\Models\Location::where('_parent_id', $this->getSettings()->root);

        $query->with('logo');

        if (null !== $this->getSettings()->featured) {
            $query->where('featured', 1);
        }

        if ($this->getSettings()->limit > 0) {
            $query->limit($this->getSettings()->limit);
        }

        switch ($this->getSettings()->sort) {
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

        $locations = $query->get();

        if ('alpha' == $this->getSettings()->sort) {
            $locations = $locations->orderBy('name', 'asc', locale()->getLocale());
        }

        if ('featured_alpha' == $this->getSettings()->sort) {
            $locations = $locations->orderBy('name', 'asc', locale()->getLocale())->orderBy('featured', 'desc');
        }

        return $locations;
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
