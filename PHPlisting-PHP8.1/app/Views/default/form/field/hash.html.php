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
<div class="input-group">
<input<?php echo $view->attributes; ?>>
<?php
    echo '<div class="input-group-append">
        <span id="' . e($view->attributes->id) . '-refresh" class="input-group-text"><i class="fas fa-sync-alt"></i></span>
    </div>';
?>
</div>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
<?php
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        $(\'#' . e($view->attributes->id) . '\').hash({
            \'url\': \'' . route('ajax/hash') . '\',
        });
    });
</script>');
?>
