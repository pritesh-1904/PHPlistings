<?php
    $id = rand(1, 99999);
    
    layout()->addCss('<link rel="stylesheet" href="' . asset('js/leaflet/leaflet.css') . '" />');
    layout()->addFooterJs('<script src="' . asset('js/leaflet/leaflet.js') . '"></script>');

    layout()->addCss('<link rel="stylesheet" href="' . asset('js/leaflet-fullscreen/leaflet.fullscreen.css') . '" />');
    layout()->addFooterJs('<script src="' . asset('js/leaflet-fullscreen/Leaflet.fullscreen.min.js') . '"></script>');

    layout()->addCss('<link rel="stylesheet" href="' . asset('js/leaflet-markercluster/MarkerCluster.css') . '" />');
    layout()->addCss('<link rel="stylesheet" href="' . asset('js/leaflet-markercluster/MarkerCluster.Default.css') . '" />');
    layout()->addFooterJs('<script src="' . asset('js/leaflet-markercluster/leaflet.markercluster.js') . '"></script>');

    layout()->addFooterJs('<script src="' . asset('js/leaflet-markers/L.Icon.FontAwesome.js') . '"></script>');
    layout()->addCss('<link rel="stylesheet" href="' . asset('js/leaflet-markers/L.Icon.FontAwesome.css') . '" />');
    
    $sort = [
        __('listing.search.form.label.sort') => request()->urlWithQuery(['page' => null, 'sort' => null, 'sort_direction' => null]),
        __('listing.search.form.label.sort_distance') => request()->urlWithQuery(['page' => null, 'sort' => 'distance', 'sort_direction' => null]),
        __('listing.search.form.label.sort_relevance') => request()->urlWithQuery(['page' => null, 'sort' => 'relevance', 'sort_direction' => null]),
        __('listing.search.form.label.sort_newest') => request()->urlWithQuery(['page' => null, 'sort' => 'newest', 'sort_direction' => null]),
        __('listing.search.form.label.sort_highest_rated') => request()->urlWithQuery(['page' => null, 'sort' => 'highest-rated', 'sort_direction' => null]),
        __('listing.search.form.label.sort_most_popular') => request()->urlWithQuery(['page' => null, 'sort' => 'most-popular', 'sort_direction' => null]),
        __('listing.search.form.label.sort_title') => request()->urlWithQuery(['page' => null, 'sort' => 'title', 'sort_direction' => null]),
    ];
?>
<section class="widget listing-search-results-widget <?php echo $view->settings->colorscheme; ?> py-6">
    <div class="container">
        <div class="row">
            <?php if (null !== $view->settings->refine) { ?>
            <div class="col-12 col-xl-3 col-lg-4 mb-3 order-2 order-lg-1">
                <?php echo $view->form->render('form/vertical'); ?>
            </div>
            <?php } ?>
            <div class="col-12<?php echo (null !== $view->settings->refine) ? ' col-xl-9 col-lg-8' : ''; ?> mb-3 order-1 order-lg-2">
                <div class="row align-items-center mb-5">
                    <div class="col-12 col-lg-7 mt-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb p-0">
                                <li class="breadcrumb-item"><a href="<?php echo route($view->type->slug); ?>"><?php echo e($view->type->name_plural); ?></a></li>
                                <?php if (null !== $view->data->get('category')) { ?>
                                    <?php foreach ($view->data->category->ancestorsAndSelfWithoutRoot()->get() as $category) { ?>
                                        <?php if ($category->id == $view->data->category->id) { ?>
                                            <li class="breadcrumb-item active"><?php echo e($category->name); ?></li>
                                        <?php } else { ?>
                                            <li class="breadcrumb-item"><a href="<?php echo route($view->type->slug . '/' . $category->slug); ?>"><?php echo e($category->name); ?></a></li>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-12 col-lg-5 d-flex justify-content-end">
                        <?php if ($view->data->listings->count() > 0) { ?>
                        <div class="ml-2">
                            <form>
                                <div class="form">
                                    <select id="_sortby" class="custom-select custom-select-sm">
                                        <?php foreach ($sort as $name => $url) { ?>
                                            <option value="<?php echo e($url); ?>"<?php if ((string) $url == (string) request()->urlWithQuery()) echo ' selected'; ?>><?php echo e($name); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="ml-2">
                            <span class="_view btn btn-light" data-id="grid" title="<?php echo __('listing.search.label.grid'); ?>"><i class="fas fa-th"></i></span>
                        </div>
                        <div class="ml-2 d-none d-md-block">
                            <span class="_view btn btn-light" data-id="list" title="<?php echo __('listing.search.label.table'); ?>"><i class="fas fa-list"></i></span>
                        </div>
                        <?php if (null !== $view->type->localizable && null !== $view->settings->map && countListingsWithMapMarker($view->data->listings) > 0) { ?>
                        <div class="ml-2">
                            <span class="_view btn btn-light" data-id="map" title="<?php echo __('listing.search.label.map'); ?>"><i class="fas fa-map-marker-alt"></i></span>
                        </div>
                        <?php } ?>
                        <div class="ml-2">
                            <a href="<?php echo route(getRoute(), request()->get->put('format', 'rss')->toArray()); ?>" target="_blank" class="btn btn-light" title="<?php echo __('listing.search.label.rss'); ?>"><i class="fas fa-rss"></i></a>
                        </div>
                        <?php } ?>
                        <script>
                            $(document).ready(function() {
                                $('#_sortby').on('change', function() {
                                    var url = $(this).find(":selected").val();
                                    if ('' != url) {
                                        window.location.href = url;
                                    }
                                });

                                var currentView = '';

                                changeView(( (undefined === Cookies.get('_view_<?php echo $view->type->id; ?>')) ? '<?php echo $view->settings->get('default_view') ?? 'grid'; ?>' : Cookies.get('_view_<?php echo $view->type->id; ?>') ));

                                $('._view').on('click', function (e) {
                                    changeView($(this).data('id'));
                                });

                                function changeView(view)
                                {                                    
                                    if (view != currentView) {
                                        $('.map').hide();
                                        $('.listing-card-container').hide();

                                        $('.listing-card-img').removeClass('col-md-4');
                                        $('.listing-card-body').removeClass('col-md-8');
                                        $('.listing-card-container').removeClass('<?php echo (null !== $view->settings->refine) ? 'col-md-6' : 'col-md-4'; ?>');

                                        switch (view) {
                                            case 'grid':
                                                $('.listing-card-container').addClass('<?php echo (null !== $view->settings->refine) ? 'col-md-6' : 'col-md-4'; ?>');
                                                $('.listing-card-container').show();

                                                break;
                                            case 'list':
                                                $('.listing-card-img').addClass('col-md-4');
                                                $('.listing-card-body').addClass('col-md-8');
                                                $('.listing-card-container').show();

                                                break;
                                            case 'map':
                                                $('.map').show().trigger('show');

                                                break;
                                        }

                                        currentView = view;

                                        Cookies.set('_view_<?php echo $view->type->id; ?>', view, { path: '<?php echo '/' . trim(request()->basePath(), '/'); ?>' });
                                    }
                                }
                            });
                        </script>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="text-medium display-6 border-bottom pb-2">
                            <?php echo e($view->heading); ?>
                        </h2>
                    </div>
                </div>
                <div class="listing-results-container row">
                    <?php if ($view->data->listings->count() == 0) { ?>
                        <div class="col-12">
                            <p><?php echo e(__('listing.alert.no_results_found')); ?></p>
                        </div>
                    <?php } ?>

                    <div class="col-12 mb-3">
                        <?php echo $view->data->listings->links(); ?>
                    </div>

                    <?php if (null !== $view->type->localizable && null !== $view->settings->map && $view->data->listings->count() > 0) { ?>
                        <div class="col-12">
                            <div id="search-results-map-<?php echo $id; ?>" class="map"></div>
                        </div>
                    <?php } ?>

                    <?php foreach ($view->data->listings as $listing) { ?>
                    <div class="listing-card-container col-12 <?php echo (null !== $view->settings->refine) ? 'col-md-6' : 'col-md-4'; ?> mb-4 px-3">
                        <?php 
                            echo view('widgets/cards/listing', [
                                'listing' => $listing,
                                'type' => $listing->type,
                                'settings' => $view->settings,
                                'data' => $view->data,
                                'bookmarking' => $view->settings->bookmarking,
                            ]); ?>
                    </div>
                    <?php } ?>

                    <div class="col-12">
                        <?php echo $view->data->listings->links(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (null !== $view->type->localizable && null !== $view->settings->map && countListingsWithMapMarker($view->data->listings) > 0) { ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#search-results-map-<?php echo $id; ?>').searchResultsMap({
            source: <?php echo generateGeoJson($view->data->listings, $view->type); ?>,
            provider: '<?php echo config()->map->provider; ?>',
            accessToken: '<?php echo config()->map->access_token; ?>',
        });
    });
</script>
<?php } ?>
<?php
function countListingsWithMapMarker($collection) {
    $count = 0;
    $processed = [];
    
    foreach ($collection as $listing) {
        if (null === $listing->type->localizable || null === $listing->get('_map')) {
            continue;
        }

        if (false === in_array($listing->id, $processed)) {
            $processed[] = $listing->id;

            $count++;
        }
    }

    return $count;
}

function generateGeoJson($collection, $type)
{
    $response = [
        'type' => 'FeatureCollection',
        'features' => [
        ],
    ];

    $processed = [];
    
    foreach ($collection as $listing) {
        if (null === $listing->type->localizable || null === $listing->get('_map')) {
            continue;
        }

        if (false === in_array($listing->id, $processed)) {
            $processed[] = $listing->id;

            $popup = '';

            if ($listing->hasRelation('logo') && false !== $listing->logo->isImage()) {
                $popup .= '<img src="' . $listing->logo->small()->getUrl() . '" class="img-fluid w-100" alt="' . e($listing->getOutputableValue('_title')) . '">';
            }

            $popup .= '
                <p class="text-secondary m-0 mb-3 display-12">
                    <a href="' . route($listing->type->slug) . '">' . e($listing->type->name_plural) . '</a> &raquo; ' . $listing->getOutputableValue('_category_links') . '
                </p>
                <p class="text-medium m-0 my-2 display-10">' . $listing->getOutputableValue('_title') . '</p>';

            if (null !== $listing->type->localizable && null !== $listing->get('_address')) {
                $popup .= '<p class="text-secondary m-0 mb-3"><i class="fas fa-map-marker-alt pr-2 text-danger"></i>' .  \strip_tags($listing->getOutputableValue('_address')) . '</p>';
            }

            if (null !== $listing->get('_page')) {
                $popup .= '<a class="btn btn-outline-primary btn-sm" href="' . route($listing->type->slug . '/' . $listing->slug) . '">' . e(__('listing.search.block.label.read_more')) . '</a>';
            }
            
            $response['features'][] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [e($listing->longitude), e($listing->latitude)],
                ],
                'properties' => [
                    'icon_color' => e($listing->category->icon_color ?? 'white'),
                    'marker_color' => e($listing->category->marker_color ?? 'red'),
                    'class' => e($listing->category->icon),
                    'popup' => $popup,
                ],
            ];
        }
    }

    return json_encode($response);
}
