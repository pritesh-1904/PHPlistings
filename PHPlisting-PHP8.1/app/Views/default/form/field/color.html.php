<?php
    layout()->addCss('<link href="' . asset('js/spectrum/spectrum.min.css') . '" rel="stylesheet">');
    layout()->addJs('<script src="' . asset('js/spectrum/spectrum.min.js') . '"></script>');
    
    $view->attributes->append('class', 'form-control');
    $view->attributes->add('type', 'text');
    $view->attributes->add('name', e($view->name));
    $view->attributes->add('value', e($view->value));

    if (isset($view->placeholder)) {
        $view->attributes->add('placeholder', e($view->placeholder));
    }
    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', e($view->attributes->id) . '-description');
    }
    if (count($view->errors) > 0) {
        $view->attributes->append('class', 'is-invalid');
    }
?>
<input<?php echo $view->attributes; ?>>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
<?php
    $format = ('rgb' == $view->get('type') || 'rgba' == $view->get('type')) ? 'rgb' : 'hex';
    $alpha = ('rgba' == $view->get('type')) ? 'true' : 'false';
    
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        $(\'#' . e($view->attributes->id) . '\').spectrum({
            preferredFormat: \'' . $format . '\',
            type: \'component\',
            locale: \'' . locale()->getLocale() . '\',
            showPalette: false,
            hideAfterPaletteSelect: true,
            showButtons: true,
            showAlpha: ' . $alpha . ',
        });
    });
</script>');
?>
