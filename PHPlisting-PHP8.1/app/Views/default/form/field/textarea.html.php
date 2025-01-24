<?php
    $view->attributes->append('class', 'form-control');
    $view->attributes->add('name', e($view->name));

    if (isset($view->placeholder)) {
        $view->attributes->add('placeholder', e($view->placeholder));
    }

    if (count($view->errors) > 0) {
        $view->attributes->append('class', 'is-invalid');
    }

    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', e($view->attributes->id) . '-description');
    }

    foreach ($view->getConstraints() as $constraint) {
        if ($constraint instanceof \App\Src\Validation\MaxlengthValidator) {
            $view->attributes->add('maxlength', $constraint->getParameter());
        }
    }
?>
<textarea<?php echo $view->attributes; ?>><?php echo e($view->value); ?></textarea>
<?php if (false !== $view->attributes->has('maxlength')) { ?>
<?php
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        $(\'#' . e($view->attributes->id) . '\').count({
            \'limit\': \'' . (int) $view->attributes->maxlength . '\',
            \'template\': \'' . e(__('form.label.characters_left')) . '\',
        });
    });
</script>');
?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-counter"></small>
<?php } ?>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
