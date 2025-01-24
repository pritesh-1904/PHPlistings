<?php
    $view->value = $view->reverseTransform($view->value);

    $view->attributes->append('class', 'custom-select');
    $view->attributes->add('name', e($view->name) . '[]');

    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', e($view->attributes->id) . '-description');
    }

    if (count($view->errors) > 0) {
        $view->attributes->append('class', 'is-invalid');
    }
?>
<select<?php echo $view->attributes; ?> multiple>
<?php foreach ((array) ($view->getOptions() ?? []) as $value => $title) { ?>
    <option value="<?php echo e($value); ?>"<?php echo (in_array($value, (array) $view->value)) ? ' selected' : ''?>><?php echo e($title); ?></option>
<?php } ?>
</select>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
