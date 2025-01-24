<?php
    layout()->addCss('<link href="' . asset('js/select2/css/select2.min.css') . '" rel="stylesheet">');
    layout()->addCss('<link href="' . asset('js/select2/css/select2.' . ($view->theme ?? 'bootstrap4') . '.css') . '" rel="stylesheet">');
    layout()->addJs('<script src="' . asset('js/select2/js/select2.min.js') . '"></script>');
    layout()->addJs('<script src="' . asset('js/select2/js/i18n/' . locale()->getLocale() . '.js') . '"></script>');
    
    $view->attributes->append('class', 'form-control');
    $view->attributes->add('name', e($view->name));
    $view->attributes->add('id', e($view->name) . rand(1,99999));
    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', e($view->attributes->id) . '-description');
    }
    if (count($view->errors) > 0) {
        $view->attributes->append('class', 'is-invalid');
    }
?>
<select<?php echo $view->attributes; ?>>
    <?php if (null !== $view->value && null !== $category = \App\Models\Category::where('id', $view->value)->where('type_id', $view->get('type_id'))->first()) { ?>
        <option value="<?php echo e($view->value); ?>" selected><?php echo $category->ancestorsAndSelfWithoutRoot()->get(['id', 'name'])->pluck('name')->implode('&raquo;'); ?></option>
    <?php } ?>
</select>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
<?php
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        $(\'#' . e($view->attributes->id) . '\').select2({
            theme: \'' . ($view->theme ?? 'bootstrap4') . '\',
            ajax: {
                url: \'' . route('ajax/category') . '\',
                dataType: \'json\',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term,
                        type_id: \'' . $view->get('type_id') . '\',
                        page: params.page || 1,
                    };
                },
            },
            minimumInputLength: 3,
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
