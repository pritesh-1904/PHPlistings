<?php
    $view->attributes->append('class', 'form-control');
    $view->attributes->add('type', 'file');
    $view->attributes->add('name', e($view->name));

    if (isset($view->placeholder)) {
        $view->attributes->add('placeholder', e($view->placeholder));
    }

    $view->attributes->add('value', ($view->hasErrors() ? e($view->value) : e($view->reverseTransform($view->value))));

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
