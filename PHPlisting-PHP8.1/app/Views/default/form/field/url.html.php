<?php
    $view->attributes->append('class', 'form-control');
    $view->attributes->add('type', 'text');
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

    foreach ($view->getConstraints() as $constraint) {
        if ($constraint instanceof \App\Src\Validation\MaxlengthValidator) {
            $view->attributes->add('maxlength', $constraint->getParameter());
        }
    }
?>
<input<?php echo $view->attributes; ?>>
<?php if (false !== $view->attributes->has('maxlength')) { ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#<?php echo e($view->attributes->id); ?>').count({
            'limit': '<?php echo (int) $view->attributes->maxlength; ?>',
            'template': '<?php echo e(__('form.label.characters_left')); ?>'
        });
    });
</script>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-counter"></small>
<?php } ?>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
