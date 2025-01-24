<?php
    $view->attributes->append('class', 'custom-switch-input');
    $view->attributes->add('type', 'checkbox');
    $view->attributes->add('name', e($view->name));

    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', e($view->attributes->id) . '-description');
    }

    if (count($view->errors) > 0) {
        $view->attributes->append('class', 'is-invalid');
    }

    foreach ($view->getOptions() as $value => $title) {
        $view->attributes->add('checked', false);
        $view->attributes->add('value', e($value));

        if ($value == $view->value) {
            $view->attributes->add('checked', true);
        }
?>
<div class="switch-content">
    <div class="custom-switch">
        <input<?php echo $view->attributes; ?>>
        <label class="custom-switch-btn" for="<?php echo e($view->attributes->id); ?>"></label>
    </div>
</div>
<?php
    }
?>
<?php if (isset($view->description)) { ?>
    <small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
