<?php
    $view->attributes->append('class', 'form-control');
    $view->attributes->add('type', 'password');
    $view->attributes->add('name', e($view->name));
    $view->attributes->add('value', $view->value);
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
<?php if (false !== $view->attributes->has('maxlength')) { ?>
<?php
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        $(\'#' . e($view->attributes->id) . '\').count({
            \'limit\': \'' . (int) $view->attributes->maxlength . '\',
            \'message\': \'' . e(__('form.label.limit_reached')) . '\',
        });
    });
</script>');
?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-counter"></small>
<?php } ?>
