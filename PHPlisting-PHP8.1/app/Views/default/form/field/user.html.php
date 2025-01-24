<?php
    layout()->addCss('<link href="' . asset('js/select2/css/select2.min.css') . '" rel="stylesheet">');
    layout()->addCss('<link href="' . asset('js/select2/css/select2.bootstrap4.css') . '" rel="stylesheet">');
    layout()->addJs('<script src="' . asset('js/select2/js/select2.min.js') . '"></script>');
    
    $view->attributes->append('class', 'form-control');
    $view->attributes->add('name', e($view->name));
    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', e($view->attributes->id) . '-description');
    }
    if (count($view->errors) > 0) {
        $view->attributes->append('class', 'is-invalid');
    }
?>
<select<?php echo $view->attributes; ?>>
    <?php if (null !== $view->value && null !== $user = \App\Models\User::find($view->value)) { ?>
        <option value="<?php echo e($view->value); ?>" selected><?php echo $user->getName(); ?> (id: <?php echo $user->id; ?>)</option>
    <?php } ?>
</select>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
<?php
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        $(\'#' . e($view->attributes->id) . '\').select2({
            theme: \'bootstrap4\',
            ajax: {
                url: \'' . route('ajax/user') . '\',
                dataType: \'json\',
                delay: 250,
            },
            placeholder: \'' . e($view->get('placeholder')) . '\',
            dropdownAutoWidth: true,
            width: null,
            escapeMarkup: function (text) { return text; },
            allowClear: true,
            language: \'' . locale()->getLocale() . '\',
        });
    });
</script>');
?>
