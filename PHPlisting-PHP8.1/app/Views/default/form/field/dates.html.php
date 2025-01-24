<?php
    layout()->addCss('<link href="' . asset('js/flatpickr/flatpickr.min.css?v=4613') . '" rel="stylesheet">');
    layout()->addFooterJs('<script src="' . asset('js/flatpickr/flatpickr.min.js?v=4613') . '"></script>');
    
    $view->attributes->append('class', 'form-control');
    $view->attributes->add('type', 'text');
    $view->attributes->add('name', e($view->name));
    $view->attributes->add('id', e($view->name) . rand(1,99999));

    if (isset($view->placeholder)) {
        $view->attributes->add('placeholder', e($view->placeholder));
    }
    $view->attributes->add('value', $view->reverseTransform($view->value));

    if (null !== $view->get('description')) {
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
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        $(\'#' . e($view->attributes->id) . '\').flatpickr({
            \'mode\': \'multiple\',
            \'dateFormat\': \'' . locale()->getDateFormat() . '\',
            \'disableMobile\': true,
            \'locale\': \'' . locale()->getLocale() . '\',
        });
    });
</script>');
?>
