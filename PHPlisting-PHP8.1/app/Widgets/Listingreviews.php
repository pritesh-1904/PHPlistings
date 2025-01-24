<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Listingreviews
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
        if (null === $this->getData()->get('listing')) {
            return null;
        }

        if (null === $this->getData()->listing->get('_reviews')) {
            return null;
        }

        $reviews = $this->getReviews();

        if ($reviews->count() == 0) {
            return null;
        }

        $this->rendered = true;

        return view('widgets/listing-reviews', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
            'reviews' => $reviews,
            'averages' => $this->getRatingAverages(),
            'categoryAverages' => $this->getRatingCategoryAverages(),
            'categories' => $this->getRatingCategories(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-white',
            'heading' => '{"en":"Reviews"}',
            'description' => '{"en":"Listing Reviews"}',
            'show_summary' => 1,
            'sort' => 'newest',
        ]);
    }

    public function getForm()
    {
         return form()
            ->add('colorscheme', 'select', ['label' => __('widget.listingreviews.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.listingreviews.form.label.white'),
                'bg-light' => __('widget.listingreviews.form.label.light'),
             ]])
            ->add('heading', 'translatable', ['label' => __('widget.listingreviews.form.label.heading')])
            ->add('description', 'translatable', ['label' => __('widget.listingreviews.form.label.description')])
            ->add('show_summary', 'toggle', ['label' => __('widget.listingreviews.form.label.summary')])
            ->add('sort', 'select', ['label' => __('widget.listingreviews.form.label.sort'), 'options' => [
                'newest' => __('widget.listingreviews.form.label.sort_newest'),
                'oldest' => __('widget.listingreviews.form.label.sort_oldest'),
            ], 'constraints' => 'required']);
    }

    public function getRatingAverages()
    {
        if (null !== $this->getSettings()->show_summary) {
            return $this->getData()->listing->reviews()
                ->select(db()->expr()->count('*', 'total'))
                ->where('active', 1)
                ->groupBy('rating')
                ->get(['rating']);
        }

        return null;
    }

    public function getRatingCategoryAverages()
    {
        if (null !== $this->getSettings()->show_summary) {
            $query = $this->getData()->listing->reviews()
                ->where('active', 1);

            foreach ($this->getRatingCategories() as $category) {
                $query->select(db()->expr()->avg('rating_' . $category->id, 'average_' . $category->id));
            }

            return $query->first([1]);
        }

        return null;
    }

    public function getRatingCategories()
    {
        return $this->getData()->listing->type->ratings()->orderBy('weight')->get();
    }

    public function getReviews()
    {        
        $reviews = \App\Models\Review::search()
            ->where('listing_id', $this->getData()->listing->id)
            ->orderBy('id', ($this->getSettings()->sort == 'newest' ? 'desc' : 'asc'))
            ->with([
                'user',
                'comments' => function ($query) {
                    return $query
                        ->where('active', 1)
                        ->orderBy('id', 'desc');
                }
            ]);

        if (false !== auth()->check()) {
            $reviews->where(function ($query) {
                return $query
                    ->where('active', '1')
                    ->orWhere(function ($query) { 
                        $query
                            ->whereNull('active')
                            ->where('user_id', auth()->user()->id);
                    });
                });
        } else {
            $reviews->where('active', '1');
        }

        return $reviews->paginate();
    }

}
