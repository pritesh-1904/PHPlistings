<?php
    layout()->addCss('<link rel="stylesheet" href="' . asset('js/leaflet/leaflet.css') . '" />');
    layout()->addFooterJs('<script src="' . asset('js/leaflet/leaflet.js') . '"></script>');

    layout()->addCss('<link rel="stylesheet" href="' . asset('js/leaflet-fullscreen/leaflet.fullscreen.css') . '" />');
    layout()->addFooterJs('<script src="' . asset('js/leaflet-fullscreen/Leaflet.fullscreen.min.js') . '"></script>');

    $view->attributes->append('class', 'form-control');
    $view->attributes->add('type', 'hidden');
    $view->attributes->add('name', e($view->name));
    $view->attributes->add('value', $view->value);

    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', e($view->attributes->id) . '-description');
    }

    if (count($view->errors) > 0) {
        $view->attributes->append('class', 'is-invalid');
    }
?>
<div id="<?php echo e($view->attributes->id);?>-container"></div>
<div id="<?php echo e($view->attributes->id);?>-map" class="map"></div>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
<input<?php echo $view->attributes;?>>
<?php
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        $(\'#' . e($view->attributes->id) . '\').location({
            url: \'' . route('ajax/cascading') . '\',
            latitude: \'' . config()->map->latitude . '\',
            longitude: \'' . config()->map->longitude . '\',
            zoom: \'' . config()->map->zoom. '\',
            message: \'' . __('form.location.alert') . '\',
            provider: \'' . config()->map->provider . '\',
            accessToken: \'' . config()->map->access_token . '\',
        });
    });
</script>');
?>
