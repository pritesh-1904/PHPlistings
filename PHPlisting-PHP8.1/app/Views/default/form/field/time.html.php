<?php
    layout()->addCss('<link href="' . asset('js/flatpickr/flatpickr.min.css?v=4613') . '" rel="stylesheet">');
    layout()->addJs('<script src="' . asset('js/flatpickr/flatpickr.min.js?v=4613') . '"></script>');
    
    $view->attributes->append('class', 'form-control');
    $view->attributes->add('type', 'text');
    $view->attributes->add('name', e($view->name));

    if (isset($view->placeholder)) {
        $view->attributes->add('placeholder', e($view->placeholder));
    }

    $view->attributes->add('value', $view->reverseTransform($view->value));
    
    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', e($view->attributes->id) . '-description');
    }
    if (count($view->errors) > 0) {
        $view->attributes->append('class', 'is-invalid');
    }

    $id = e($view->attributes->id);

    unset($view->attributes->id);
?>
<div id="<?php echo $id; ?>" class="input-group">
    <input<?php echo $view->attributes; ?> data-input>
    <div class="input-group-append" data-clear>
        <span class="input-group-text"><i class="fas fa-times"></i></span>
    </div>
</div>  
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo $id; ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
<?php
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        $(\'#' . $id .'\').flatpickr({
            \'mode\': \'single\',
            \'wrap\': true,
            \'dateFormat\': \'' . str_replace('A', 'K', locale()->getTimeFormat()) . '\',
            \'enableTime\': true,
            \'noCalendar\': true,
            \'disableMobile\': true,
            \'time_24hr\': ' . (locale()->getTimeFormat() == 'H:i' ? 'true' : 'false') . '
        });
    });
</script>');
?>
