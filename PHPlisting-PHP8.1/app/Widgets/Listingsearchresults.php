<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2023 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Widgets;

class Listingsearchresults
    extends \App\Src\Widget\BaseWidget
{

    protected $type = null;

    public function isMultiInstance()
    {
        return true;
    }

    public function compile()
    {
        if (null === $this->getType()) {
            return null;
        }

        $widgets = $this->getWidgetizer()->all();

        foreach ($widgets as $widget) {
            if ($widget->slug == 'listingsearchresults' && $widget->getWidgetObject()->isCompiled()) {
                return null;
            }
        }

        if (null !== $this->getData()->get('category')) {
            request()->get->put('category_id', $this->getData()->category->id);
        }

        if (null !== $this->getData()->get('location')) {
            request()->get->put('location_id', $this->getData()->location->id);
        }

        session()->put('_search_' . $this->getType()->slug, route(getRoute(), request()->get->all()));

        $listings = $this->getListings();

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
        
        $this->getData()->put('listings', $listings);

        if ($listings->count() > 0) {
            (new \App\Repositories\Statistics)->push('listing_search_impression', $listings->pluck('id')->all());
        }

        $this->compiled = true;
    }
    
    public function render()
    {
        if (null === $this->getType()) {
            return null;
        }

        $widgets = $this->getWidgetizer()->all();

        foreach ($widgets as $widget) {
            if ($widget->slug == 'listingsearchresults' && $widget->getWidgetObject()->isRendered()) {
                return null;
            }
        }

        $this->rendered = true;

        if (null !== request()->get->get('format') && 'rss' == request()->get->get('format')) {
            $repository = new \App\Repositories\Rss();

            foreach ($this->getData()->get('listings') as $listing) {
                $repository->push($listing);
            }

            return response($repository->render(route(getRoute(), request()->get->toArray(), locale()->getLocale(), '&amp;'), $this->getType()->name_plural, config()->general->site_name))
                ->withHeaders([
                    'Content-type' => 'text/xml;charset=' . config()->app->charset,
                ]);
        }

        return view('widgets/listing-search-results', [
            'settings' => $this->getSettings(),
            'data' => $this->getData(),
            'form' => $this->getRefineForm(),
            'heading' => $this->getHeading(),
            'type' => $this->getType(),
        ]);
    }

    public function getDefaultSettings()
    {
        return collect([
            'colorscheme' => 'bg-white',
            'default_type_id' => '0',
            'limit' => '10',
            'distance_type' => 'miles',
            'refine' => '1',
            'hide_empty' => null,
            'map' => '1',
            'bookmarking' => '1',
            'sort' => 'alpha',
            'default_logo' => bin2hex(random_bytes(16)),
            'default_view' => 'grid',
        ]);
    }

    public function getForm()
    {
        return form()
            ->add('colorscheme', 'select', ['label' => __('widget.listingsearchresults.form.label.colorscheme'), 'options' => [
                'bg-white' => __('widget.listingsearchresults.form.label.white'),
                'bg-light' => __('widget.listingsearchresults.form.label.light'),
             ]])
            ->add('default_type_id', 'select', ['label' => __('widget.listingsearchresults.form.label.type'), 'options' => [0 => __('widget.listingsearchresults.form.label.type.auto')] + \App\Models\Type::whereNull('deleted')->get()->pluck('name_plural', 'id')->all(), 'constraints' => 'required|number'])
            ->add('limit', 'number', ['label' => __('widget.listingsearchresults.form.label.limit'), 'constraints' => 'required|min:0|max:100'])
            ->add('distance_type', 'select', ['label' => __('widget.listingsearchresults.form.label.distance_type'), 'options' => ['miles' => __('widget.listingsearchresults.form.label.distance_type.miles'), 'kilometers' => __('widget.listingsearchresults.form.label.distance_type.kilometers')], 'constraints' => 'required'])
            ->add('refine', 'toggle', ['label' => __('widget.listingsearchresults.form.label.refine')])
            ->add('hide_empty', 'toggle', ['label' => __('widget.listingsearchresults.form.label.hide_empty')])
            ->add('map', 'toggle', ['label' => __('widget.listingsearchresults.form.label.map')])
            ->add('bookmarking', 'toggle', ['label' => __('widget.listingsearchresults.form.label.bookmarking')])
            ->add('sort', 'select', ['label' => __('widget.listingsearchresults.form.label.sort'), 'options' => [
                'alpha' => __('widget.listingsearchresults.form.label.sort_alpha'),
                'popular' => __('widget.listingsearchresults.form.label.sort_popular'),
                'latest' => __('widget.listingsearchresults.form.label.sort_latest'),
                'rating' => __('widget.listingsearchresults.form.label.sort_rating'),
                'upcoming' => __('widget.listingsearchresults.form.label.sort_upcoming'),
                'random' => __('widget.listingsearchresults.form.label.sort_random'),
                'featured_alpha' => __('widget.listingsearchresults.form.label.sort_featured_alpha'),
                'featured_popular' => __('widget.listingsearchresults.form.label.sort_featured_popular'),
                'featured_latest' => __('widget.listingsearchresults.form.label.sort_featured_latest'),
                'featured_rating' => __('widget.listingsearchresults.form.label.sort_featured_rating'),
                'featured_upcoming' => __('widget.listingsearchresults.form.label.sort_featured_upcoming'),
                'featured_random' => __('widget.listingsearchresults.form.label.sort_featured_random'),
            ], 'constraints' => 'required'])
            ->add('default_logo', 'dropzone', ['label' => __('widget.listingsearchresults.form.label.default_logo'), 'upload_id' => '1'])
            ->add('default_view', 'select', ['label' => __('widget.listingsearchresults.form.label.default_view'), 'options' => ['grid' => __('listing.search.label.grid'), 'list' => __('listing.search.label.table')]]);
    }

    public function getRefineForm()
    {
        $sort = [
            '' => '',
            'distance' => __('listing.search.form.label.sort_distance'),
            'relevance' => __('listing.search.form.label.sort_relevance'),
            'newest' => __('listing.search.form.label.sort_newest'),
            'highest-rated' => __('listing.search.form.label.sort_highest_rated'),
            'most-popular' => __('listing.search.form.label.sort_most_popular'),
            'title' => __('listing.search.form.label.sort_title'),
        ];

        $form = form()
            ->setMethod('get')
            ->setAction(route($this->getType()->slug . '/search'))
            ->add('keyword', 'text', [
                'label' => __('listing.search.form.label.keyword'),
                'weight' => -60
            ]);

        if ('Event' == $this->getType()->type) {
            $form
                ->add('dates', 'dates', [
                    'label' => __('listing.search.form.label.dates'),
                    'weight' => -50,
                ]);
        }

        if (null === $this->getData()->get('category')) {
            $form
                ->add('category_id', 'cascading', [
                    'label' => __('listing.search.form.label.category'),
                    'cascading_source' => 'category',
                    'cascading_type_id' => $this->getType()->id,
                    'cascading_hide_inactive' => '1',
                    'cascading_hide_empty' => (null === $this->getSettings()->get('hide_empty') ? 0 : 1),
                    'weight' => -40
                ]);
        } else {
            $form
                ->add('category_id', 'hidden', ['value' => $this->getData()->get('category')->id]);
        }

        if (null !== $this->getType()->localizable) {
            $form
                ->add('location_id', 'cascading', [
                    'label' => __('listing.search.form.label.location'),
                    'cascading_source' => 'location',
                    'weight' => -30
                ])
                ->add('radius', 'select', [
                    'options' => [
                        '' => __('listing.search.form.label.radius_option'),
                        '1' => '1',
                        '5' => '5',
                        '10' => '10',
                        '15' => '15',
                        '20' => '20',
                        '25' => '25',
                        '50' => '50',
                        '100' => '100'
                    ],
                    'label' => __('listing.search.form.label.radius'),
                    'weight' => -20,
                ]);
        }

        $form
            ->add('sort', 'select', [
                'label' => __('listing.search.form.label.sort'),
                'options' => $sort,
                'weight' => 1000000
            ])
            ->add('submit', 'submit', [
                'label' => __('listing.search.form.label.submit_refine')
            ])
            ->add('reset', 'button', ['label' => __('listing.search.form.label.reset'), 'attributes' => ['href' => route($this->getType()->slug . '/search')]])
            ->bindModel($this->getEmptyListingModel(), 'search')
            ->forceRequest();

        return $form;
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

    public function getListings()
    {
        $listings = $this->getListingsQuery()
            ->paginate($this->getSettings()->limit);

        return $listings;
    }
    
    private function getEmptyListingModel()
    {
        $model = new \App\Models\Listing(['type_id' => $this->getType()->id]);

        if (null !== $this->getData()->get('category')) {
            $model->category_id = $this->getData()->category->id;
        }

        $model->getQuery()
            ->where($model->getPrefixedTable() . '.active', '1')
            ->where($model->getPrefixedTable() . '.status', 'active')
            ->orderBy($model->getPrefixedTable() . '._position');

        return $model;
    }

    private function getListingsQuery()
    {
        $model = $this->getEmptyListingModel();
        
        if ('Event' == $this->getType()->type) {
            $model->getQuery()
                ->select(db()->raw('dates.event_date AS event_date'))
                ->rightJoin(
                    $model->getQuery()->getConnection()->getPrefix() . 'dates',
                    'dates.listing_id = ' . $model->getPrefixedTable() . '.id',
                    'dates'
                )
            ;
        }

        $query = \App\Models\Listing::search($model, ['distance_type' => $this->getSettings()->distance_type])
            ->where($model->getPrefixedTable() . '.type_id', $this->getType()->id);

        if (null === request()->get->get('sort') || '' == request()->get->get('sort')) {
            switch ($this->getSettings()->sort) {
                case 'popular': 
                    $query->orderBy($model->getPrefixedTable() . '.impressions', 'desc');
                    break;
                case 'latest': 
                    $query->orderBy($model->getPrefixedTable() . '.id', 'desc');
                    break;
                case 'rating': 
                    $query->orderBy($model->getPrefixedTable() . '.rating', 'desc');
                    break;
                case 'upcoming':
                    if ('Event' == $this->getType()->type) {
                        $query
                            ->orderBy(db()->raw('ABS(DATEDIFF(NOW(), event_date))'))
                            ->having('event_date >= CURDATE()');
                        break;
                    }
                case 'random':
                    if (false === session()->has('randomization_seed') || 1 == request()->get->get('page', 1)) {
                        session()->put('randomization_seed', rand(1, 999999));
                    }

                    $query->orderBy(db()->raw('RAND(' . session()->get('randomization_seed') . ')'));
                    break;
                case 'featured_alpha': 
                    $query->orderBy($model->getPrefixedTable() . '._featured', 'desc');
                    $query->orderBy($model->getPrefixedTable() . '.title');
                    break;
                case 'featured_popular': 
                    $query->orderBy($model->getPrefixedTable() . '._featured', 'desc');
                    $query->orderBy($model->getPrefixedTable() . '.impressions', 'desc');
                    break;
                case 'featured_latest': 
                    $query->orderBy($model->getPrefixedTable() . '._featured', 'desc');
                    $query->orderBy($model->getPrefixedTable() . '.id', 'desc');
                    break;
                case 'featured_rating': 
                    $query->orderBy($model->getPrefixedTable() . '._featured', 'desc');
                    $query->orderBy($model->getPrefixedTable() . '.rating', 'desc');
                    break;
                case 'featured_upcoming':
                    if ('Event' == $this->getType()->type) {
                        $query
                            ->orderBy($model->getPrefixedTable() . '._featured', 'desc')
                            ->orderBy(db()->raw('ABS(DATEDIFF(NOW(), event_date))'))
                            ->having('event_date >= CURDATE()');
                        break;
                    }
                case 'featured_random':
                    if (false === session()->has('randomization_seed') || 1 == request()->get->get('page', 1)) {
                        session()->put('randomization_seed', rand(1, 999999));
                    }

                    $query->orderBy($model->getPrefixedTable() . '._featured', 'desc');
                    $query->orderBy(db()->raw('RAND(' . session()->get('randomization_seed') . ')'));
                    break;
                default: 
                    $query->orderBy($model->getPrefixedTable() . '.title');
                    break;
            }
        }

        $query->with(['type', 'category', 'data']);

        if (null !== $this->getType() && null !== $this->getType()->localizable) {
            $query->with('location');
        }

        return $query;
    }
    
    public function getHeading()
    {
        $elements = [];

        foreach (['category', 'keyword', 'location'] as $key) {
            $value = null;
            
            if ('keyword' == $key) {
                if (null !== request()->get->get('keyword') && '' != request()->get->get('keyword')) {
                    $value = request()->get->get('keyword');
                }
            } else {
                if (null !== $this->getData()->get($key)) {
                    $value = $this->getData()->get($key)->name;
                }
            }

            $elements[$key] = __(
                'listing.search.header.' . $key,
                [$key => $value],
                (null !== $value ? 2 : 1)
            );
        }

        $elements['name_singular'] = mb_strtolower($this->getType()->get('name_singular'));
        $elements['name_plural'] = mb_strtolower($this->getType()->get('name_plural'));

        return __('listing.search.header', $elements);
    }

}
