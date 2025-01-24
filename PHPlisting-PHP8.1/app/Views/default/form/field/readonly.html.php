<?php
    $view->attributes->append('class', 'form-control form-control-readonly');
    $view->attributes->add('type', 'text');
    $view->attributes->add('name', e($view->name));
    $view->attributes->add('value', $view->reverseTransform($view->value));
?>
<input<?php echo $view->attributes; ?> readonly>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
