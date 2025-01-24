<?php
    $view->attributes->append('class', 'form-control');
    $view->attributes->add('type', 'hidden');
    $view->attributes->add('name', e($view->name));
    $view->attributes->add('value', $view->value);

    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', $view->attributes->id . '-description');
    }

    if (count($view->errors) > 0) {
        $view->attributes->append('class', 'is-invalid');
    }
?>
<div id="<?php echo e($view->attributes->id);?>-container"></div>
<input<?php echo $view->attributes;?>>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
<?php
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        $(\'#' . e($view->attributes->id) .'\').cascading({
            url: \'' . route('ajax/cascading') . '\',
            source: \'' . e($view->cascading_source ?? 'location') . '\',
            type_id: \'' . e($view->cascading_type_id ?? '0') . '\',
            hide_inactive: \'' . e($view->cascading_hide_inactive ?? '0') . '\',
            hide_empty: \'' . e($view->cascading_hide_empty ?? '0') . '\',
        });
    });
</script>');
?>
