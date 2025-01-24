<?php
    layout()->addCss('<link href="' . asset('js/nouislider/nouislider.css') . '" rel="stylesheet">');
    layout()->addJs('<script src="' . asset('js/nouislider/nouislider.min.js') . '"></script>');

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
<small class="form-text text-muted pt-1" id="<?php echo e($view->attributes->id); ?>-counter"></small>
<input<?php echo $view->attributes;?>>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
<?php
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        $(\'#' . e($view->attributes->id) . '\').range({
            \'min\': \'' . e($view->range_min ?? 0) . '\',
            \'max\': \'' . e($view->range_max ?? 100) . '\',
            \'step\': \'' . e($view->range_step ?? 1) . '\',
            \'template\': \'' . e($view->range_prefix ?? '') . ' {min} ' . e($view->range_suffix ?? '') . ' - ' . e($view->range_prefix ?? '') . ' {max} ' . e($view->range_suffix ?? '') . '\',
            \'direction\': \'' . locale()->getDirection() . '\',
        });        
    });
</script>');
?>
