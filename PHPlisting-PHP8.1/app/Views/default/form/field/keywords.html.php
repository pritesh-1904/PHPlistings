<?php
    layout()->addCss('<link href="' . asset('js/selectize/selectize.bootstrap4.css') . '" rel="stylesheet">');
    layout()->addJs('<script src="' . asset('js/selectize/selectize.min.js') . '"></script>');
    
    $view->attributes->append('class', 'form-control');
    $view->attributes->add('type', 'text');
    $view->attributes->add('name', e($view->name));
    $view->attributes->add('value', $view->value);
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
<?php
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        $(\'#' .  e($view->attributes->id) . '\').selectize({
            delimiter: \',\',
            persist: false,
            create: function(input) {
                return {
                    value: input,
                    text: input
                }
            }
        });
    });
</script>');
?>
