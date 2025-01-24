<?php
    $id = 'map-' . rand(1,999);

    layout()->addCss('<link rel="stylesheet" href="' . asset('js/leaflet/leaflet.css') . '" />');
    layout()->addFooterJs('<script src="' . asset('js/leaflet/leaflet.js') . '"></script>');

    layout()->addCss('<link rel="stylesheet" href="' . asset('js/leaflet-fullscreen/leaflet.fullscreen.css') . '" />');
    layout()->addFooterJs('<script src="' . asset('js/leaflet-fullscreen/Leaflet.fullscreen.min.js') . '"></script>');

    layout()->addCss('<link rel="stylesheet" href="' . asset('js/leaflet-markercluster/MarkerCluster.css') . '" />');
    layout()->addCss('<link rel="stylesheet" href="' . asset('js/leaflet-markercluster/MarkerCluster.Default.css') . '" />');
    layout()->addFooterJs('<script src="' . asset('js/leaflet-markercluster/leaflet.markercluster.js') . '"></script>');

    layout()->addFooterJs('<script src="' . asset('js/leaflet-markers/L.Icon.FontAwesome.js') . '"></script>');
    layout()->addCss('<link rel="stylesheet" href="' . asset('js/leaflet-markers/L.Icon.FontAwesome.css') . '" />');
?>
<div id="<?php echo $id; ?>" class="map"></div>
<?php
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        $(\'#' . $id . '\').widgetMap({
            url: \'' . route('ajax/map') . '\',
            latitude: \'' . $view->settings->latitude . '\',
            longitude: \'' . $view->settings->longitude . '\',
            zoom: \'' . $view->settings->zoom . '\',
            type_id: \'' . $view->type->id . '\',
            provider: \'' . config()->map->provider . '\',
            accessToken: \'' . config()->map->access_token . '\',
        });
    });
</script>');
?>
