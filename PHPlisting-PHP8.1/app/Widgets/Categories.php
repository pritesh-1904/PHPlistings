<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2022 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Categories
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
        $this->rendered = true;

        if (null === $this->getType()) {
            return null;
        }

        if (null !== $this->getSettings()->get('slider')) {
            layout()->addCss('<link href="' . asset('js/swiper/css/swiper.min.css?v=844') . '" rel="stylesheet">');
            layout()->addFooterJs('<script src="' . asset('js/swiper/js/swiper.min.js?v=844') . '"></script>');
        }

        $categories = $this->getCategories();

        if ($categories->count() == 0) {
            return null;
        }

        $template = (null === $this->getSettings()->slider) ? 'widgets/categories' : 'widgets/categories-slider';
            
        return view($template, [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
            'categories' => $categories,
            'type' => $this->getType(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-light',
            'slider' => null,
            'slider_speed' => '2000',
            'slider_autoplay' => 1,
            'slider_autoplay_delay' => '10000',
            'caption' => '{"en":"Top Picks"}',
            'heading' => '{"en":"Popular Categories"}',
            'default_type_id' => '0',
            'featured' => null,
            'show_logo' => '1',
            'show_icon' => null,
            'show_description' => '1',
            'show_count' => null,
            'hide_empty' => null,
            'center' => null,
            'sort' => 'popular',
            'limit' => '8',
            'show_children' => '1',
            'limit_children' => '5',
            'sort_children' => 'alpha',
            'default_logo' => bin2hex(random_bytes(16)),
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.categories.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.categories.form.label.white'),
                'bg-light' => __('widget.categories.form.label.light'),
             ]])
            ->add('slider', 'toggle', ['label' => __('widget.categories.form.label.slider')])
            ->add('slider_speed', 'number', ['label' => __('widget.categories.form.label.slider_transition'), 'constraints' => 'required'])
            ->add('slider_autoplay', 'toggle', ['label' => __('widget.categories.form.label.slider_autoplay')])
            ->add('slider_autoplay_delay', 'number', ['label' => __('widget.categories.form.label.slider_autoplay_delay'), 'constraints' => 'required'])
            ->add('caption', 'translatable', ['label' => __('widget.categories.form.label.caption')])
            ->add('heading', 'translatable', ['label' => __('widget.categories.form.label.heading')])
            ->add('default_type_id', 'select', ['label' => __('widget.categories.form.label.type'), 'options' => [0 => __('widget.categories.form.label.type.auto')] + \App\Models\Type::whereNull('deleted')->get()->pluck('name_plural', 'id')->all(), 'constraints' => 'required|number'])
            ->add('featured', 'toggle', ['label' => __('widget.categories.form.label.featured')])
            ->add('show_logo', 'toggle', ['label' => __('widget.categories.form.label.show_logo')])
            ->add('show_icon', 'toggle', ['label' => __('widget.categories.form.label.show_icon')])
            ->add('show_description', 'toggle', ['label' => __('widget.categories.form.label.show_description')])
            ->add('show_count', 'toggle', ['label' => __('widget.categories.form.label.show_count')])
            ->add('hide_empty', 'toggle', ['label' => __('widget.categories.form.label.hide_empty')])
            ->add('center', 'toggle', ['label' => __('widget.categories.form.label.center')])
            ->add('sort', 'select', ['label' => __('widget.categories.form.label.sort'), 'options' => [
                'alpha' => __('widget.categories.form.label.sort_alpha'),
                'popular' => __('widget.categories.form.label.sort_popular'),
                'counter' => __('widget.categories.form.label.sort_counter'),
                'random' => __('widget.categories.form.label.sort_random'),
                'featured_alpha' => __('widget.categories.form.label.sort_featured_alpha'),
                'featured_popular' => __('widget.categories.form.label.sort_featured_popular'),
                'featured_counter' => __('widget.categories.form.label.sort_featured_counter'),
                'featured_random' => __('widget.categories.form.label.sort_featured_random'),
            ], 'constraints' => 'required'])
            ->add('limit', 'number', ['label' => __('widget.categories.form.label.limit'), 'constraints' => 'required'])
            ->add('show_children', 'toggle', ['label' => __('widget.categories.form.label.show_children')])
            ->add('limit_children', 'number', ['label' => __('widget.categories.form.label.limit_children'), 'constraints' => 'required'])
            ->add('sort_children', 'select', ['label' => __('widget.categories.form.label.sort_children'), 'options' => [
                'alpha' => __('widget.categories.form.label.sort_alpha'),
                'popular' => __('widget.categories.form.label.sort_popular'),
                'counter' => __('widget.categories.form.label.sort_counter'),
                'random' => __('widget.categories.form.label.sort_random'),
                'featured_alpha' => __('widget.categories.form.label.sort_featured_alpha'),
                'featured_popular' => __('widget.categories.form.label.sort_featured_popular'),
                'featured_counter' => __('widget.categories.form.label.sort_featured_counter'),
                'featured_random' => __('widget.categories.form.label.sort_featured_random'),
            ], 'constraints' => 'required'])
            ->add('default_logo', 'dropzone', ['label' => __('widget.categories.form.label.default_logo'), 'upload_id' => '3']);
    }

    public function getCategories()
    {
        $query = $this->getType()->categories()
            ->where('active', '1')
            ->where('_parent_id', (new \App\Models\Category)->getRoot($this->getType()->id)->id);

        if (null !== $this->getSettings()->show_logo) {
            $query->with('logo');
        }

        if (null !== $this->getSettings()->featured) {
            $query->where('featured', 1);
        }

        if (null !== $this->getSettings()->hide_empty) {
            $query->where('counter', '>', 0);
        }

        if ($this->getSettings()->limit > 0) {
            $query->limit($this->getSettings()->limit);
        }

        switch ($this->getSettings()->sort) {
            case 'popular': 
                $query->orderBy('impressions', 'desc');
                break;
            case 'counter': 
                $query->orderBy('counter', 'desc');
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
            case 'featured_counter': 
                $query->orderBy('featured', 'desc');
                $query->orderBy('counter', 'desc');
                break;
            case 'featured_random': 
                $query->orderBy('featured', 'desc');
                $query->orderBy(db()->raw('RAND()'));
                break;
            default: 
                $query->orderBy('name');
                break;
        }

        if (null !== $this->getSettings()->show_children) {
            $query->with('children', function ($query) {
                $query->where('active', 1);
                
                if (null !== $this->getSettings()->hide_empty) {
                    $query->where('counter', '>', 0);
                }
                
                switch ($this->getSettings()->sort_children) {
                    case 'popular': 
                        $query->orderBy('impressions', 'desc');
                        break;
                    case 'counter': 
                        $query->orderBy('counter', 'desc');
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
                    case 'featured_counter': 
                        $query->orderBy('featured', 'desc');
                        $query->orderBy('counter', 'desc');
                        break;
                    case 'featured_random': 
                        $query->orderBy('featured', 'desc');
                        $query->orderBy(db()->raw('RAND()'));
                        break;
                    default: 
                        $query->orderBy('name');
                        break;
                }
            });
        }

        $categories = $query->get();

        if ('alpha' == $this->getSettings()->sort) {
            $categories = $categories->orderBy('name', 'asc', locale()->getLocale());
        }

        if (null !== $this->getSettings()->show_children && 'alpha' == $this->getSettings()->sort_children) {
            foreach ($categories as $category) {
                $category = $category->children->orderBy('name', 'asc', locale()->getLocale());
            }
        }

        if ('featured_alpha' == $this->getSettings()->sort) {
            $categories = $categories->orderBy('name', 'asc', locale()->getLocale())->orderBy('featured', 'desc');
        }

        if (null !== $this->getSettings()->show_children && 'featured_alpha' == $this->getSettings()->sort_children) {
            foreach ($categories as $category) {
                $category = $category->children->orderBy('name', 'asc', locale()->getLocale())->orderBy('featured', 'desc');
            }
        }

        return $categories;
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
