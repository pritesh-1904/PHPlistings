<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Reviews
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

        if (null === $this->getType()->reviewable) {
            return null;
        }

        if (null !== $this->getSettings()->get('slider')) {
            layout()->addCss('<link href="' . asset('js/swiper/css/swiper.min.css?v=844') . '" rel="stylesheet">');
            layout()->addFooterJs('<script src="' . asset('js/swiper/js/swiper.min.js?v=844') . '"></script>');
        }

        $reviews = $this->getReviews();

        if ($reviews->count() == 0) {
            return null;
        }

        if (null !== $this->getSettings()->get('show_logo')) {
            $ids = [];

            foreach ($reviews as $review) {
                $field = $review->listing->data->where('field_name', 'logo_id')->where('value', '!=', '')->first();
                if (null !== $field && null !== $field->active) {
                    $ids[] = $field->value;
                }
            }

            if (count($ids) > 0) {
                $logos = \App\Models\File::whereIn('document_id', $ids)->where('uploadtype_id', 1)->get();
                foreach ($reviews as $review) {
                    foreach ($logos as $logo) {
                        $field = $review->listing->data->where('field_name', 'logo_id')->first();
                        if ($logo->document_id == $field->value) {
                            $review->listing->setRelation('logo', $logo);
                        }
                    }
                }
            }
        }

        $template = (null === $this->getSettings()->slider) ? 'widgets/reviews' : 'widgets/reviews-slider';

        $this->rendered = true;

        return view($template, [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
            'reviews' => $reviews,
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
            'caption' => '{"en":"Feedback"}',
            'heading' => '{"en":"Latest Reviews"}',
            'default_type_id' => '0',
            'featured' => null,
            'rating' => '0;5',
            'length' => '150',
            'show_logo' => null,
            'linked' => '',
            'sort' => 'latest',
            'limit' => '8',
            'default_logo' => bin2hex(random_bytes(16)),
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.reviews.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.reviews.form.label.white'),
                'bg-light' => __('widget.reviews.form.label.light'),
             ]])
            ->add('slider', 'toggle', ['label' => __('widget.reviews.form.label.slider')])
            ->add('slider_speed', 'number', ['label' => __('widget.reviews.form.label.slider_transition'), 'constraints' => 'required'])
            ->add('slider_autoplay', 'toggle', ['label' => __('widget.reviews.form.label.slider_autoplay')])
            ->add('slider_autoplay_delay', 'number', ['label' => __('widget.reviews.form.label.slider_autoplay_delay'), 'constraints' => 'required'])
            ->add('caption', 'translatable', ['label' => __('widget.reviews.form.label.caption')])
            ->add('heading', 'translatable', ['label' => __('widget.reviews.form.label.heading')])
            ->add('default_type_id', 'select', ['label' => __('widget.reviews.form.label.type'), 'options' => [0 => __('widget.reviews.form.label.type.auto')] + \App\Models\Type::whereNull('deleted')->get()->pluck('name_plural', 'id')->all(), 'constraints' => 'required|number'])
            ->add('featured', 'toggle', ['label' => __('widget.reviews.form.label.featured')])
            ->add('rating', 'range', ['label' => __('widget.reviews.form.label.rating'), 'range_min' => '0', 'range_max' => '5', 'range_step' => '0.5', 'constraints' => 'min:0|max:5'])
            ->add('length', 'number', ['label' => __('widget.reviews.form.label.length'), 'constraints' => 'min:0|max:500'])
            ->add('show_logo', 'toggle', ['label' => __('widget.reviews.form.label.show_logo')])
            ->add('linked', 'select', ['label' => __('widget.reviews.form.label.linked'), 'options' => [
                '' => __('widget.reviews.form.label.linked_disabled'),
                'category' => __('widget.reviews.form.label.linked_category'),
                'location' => __('widget.reviews.form.label.linked_location'),
                'categorylocation' => __('widget.reviews.form.label.linked_categorylocation'),
                'user' => __('widget.reviews.form.label.linked_user'),
            ]])
            ->add('sort', 'select', ['label' => __('widget.reviews.form.label.sort'), 'options' => [
                'latest' => __('widget.reviews.form.label.sort_latest'),
                'random' => __('widget.reviews.form.label.sort_random'),
                'rating_latest' => __('widget.reviews.form.label.sort_rating_latest'),
                'rating_random' => __('widget.reviews.form.label.sort_rating_random'),
                'rev_rating_latest' => __('widget.reviews.form.label.sort_rev_rating_latest'),
                'rev_rating_random' => __('widget.reviews.form.label.sort_rev_rating_random'),
            ], 'constraints' => 'required'])
            ->add('limit', 'number', ['label' => __('widget.reviews.form.label.limit'), 'constraints' => 'required|min:1'])
            ->add('default_logo', 'dropzone', ['label' => __('widget.reviews.form.label.default_logo'), 'upload_id' => '1']);
    }

    public function getReviews()
    {
        $query = $this->getType()->reviews()
            ->whereNotNull('active')
            ->whereBetween('rating', explode(';', trim($this->getSettings()->rating)))
            ->whereHas('listing', function ($query) {
                $query
                    ->whereNotNull('_reviews')
                    ->whereNotNull('_page')
                    ->whereNotNull('active')
                    ->where('status', 'active');

                if (null !== $this->getSettings()->featured) {
                    $query->whereNotNull('_featured');
                }

                $category = null;
                $location = null;

                if ('' != $this->getSettings()->get('linked', '')) {
                    if (null !== $this->getData()->get('listing')) {
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
        });

        $query->with(['type', 'listing']);

        switch ($this->getSettings()->sort) {
            case 'latest':
                $query->orderBy('id', 'desc');
                break;
            case 'random':
                $query->orderBy(db()->raw('RAND()'));
                break;
            case 'rating_latest':
                $query->orderBy('id', 'desc');
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_random':
                $query->orderBy(db()->raw('RAND()'));
                $query->orderBy('rating', 'desc');
                break;
            case 'rev_rating_latest':
                $query->orderBy('id', 'desc');
                $query->orderBy('rating', 'asc');
                break;
            case 'rev_rating_random':
                $query->orderBy(db()->raw('RAND()'));
                $query->orderBy('rating', 'asc');
                break;
            default: 
                break;
        }

        $query->limit((int) $this->getSettings()->limit);

        $reviews = $query->get();

        return $reviews;
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
