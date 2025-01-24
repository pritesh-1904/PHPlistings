<?php
    $view->value = $view->reverseTransform($view->value);

    $view->attributes->append('class', 'custom-control-input');
    $view->attributes->add('type', 'checkbox');
    $view->attributes->add('name', e($view->name) . '[]');

    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', e($view->attributes->id) . '-description');
    }

    if (count($view->errors) > 0) {
        $view->attributes->append('class', 'is-invalid');
    }

    $count = 1;
    $id = e($view->attributes->id);

    foreach ((array) ($view->getOptions() ?? []) as $value => $title) {
        $view->attributes->add('checked', false);
        $view->attributes->add('value', $value);

        if (in_array($value, $view->value)) {
            $view->attributes->add('checked', true);
        }

        $view->attributes->add('id', $id . '_' . $count);

// For inline checkboxes add "custom-control-inline" to the wraping div
?>
<div class="custom-control-lg custom-control custom-checkbox pb-2">
    <input<?php echo $view->attributes; ?>>
    <label class="custom-control-label" for="<?php echo e($view->attributes->id); ?>"><?php echo e($title); ?></label>
</div>
<?php 
        $count++;
    }
?>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
