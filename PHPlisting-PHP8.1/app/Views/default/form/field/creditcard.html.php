<?php
    $view->attributes->append('class', 'form-control');
    $view->attributes->add('type', 'text');
    $view->attributes->add('name', e($view->name));
    if (isset($view->placeholder)) {
        $view->attributes->add('placeholder', e($view->placeholder));
    }
    $view->attributes->add('value', $view->value);
    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', e($view->attributes->id) . '-description');
    }
    if (count($view->errors) > 0) {
        $view->attributes->append('class', 'is-invalid');
    }
?>
<div class="input-group">
    <input<?php echo $view->attributes; ?>>
    <div class="input-group-append">
        <span class="input-group-text"><i class="far fa-credit-card"></i></span>
    </div>
</div>  
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo $id; ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
