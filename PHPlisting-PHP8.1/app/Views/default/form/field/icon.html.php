<?php
    layout()->addCss('<link href="' . asset('js/iconpicker/iconpicker-1.5.0.css') . '" rel="stylesheet">');
    layout()->addJs('<script src="' . asset('js/iconpicker/iconpicker-1.5.0.js') . '"></script>');
    
    $view->attributes->append('class', 'form-control');
    $view->attributes->add('type', 'hidden');
    $view->attributes->add('name', e($view->name));
    if (isset($view->placeholder)) {
        $view->attributes->add('placeholder', e($view->placeholder));
    }
    $view->attributes->add('value', $view->reverseTransform($view->value));
    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', e($view->attributes->id) . '-description');
    }
    if (count($view->errors) > 0) {
        $view->attributes->append('class', 'is-invalid');
    }

    $id = e($view->attributes->id);
?>
<div class="d-inline-flex align-items-center">
    <span class="fa-3x py-2 px-3 border rounded text-secondary" style="min-width: 5rem; min-height: 6rem;"><i id="iconpicker-preview-<?php echo $id; ?>" class="<?php echo e($view->attributes->value); ?>"></i></span>
    <button type="button" class="btn btn-primary ml-3" id="iconpicker-<?php echo $id; ?>" data-iconpicker-input="input#<?php echo $id; ?>" data-iconpicker-preview="#iconpicker-preview-<?php echo $id; ?>"><?php echo e(__('form.label.icon.select')); ?></button>
    <button type="button" class="btn btn-danger ml-1" id="iconpicker-<?php echo $id; ?>-drop" data-iconpicker-input="input#<?php echo $id; ?>" data-iconpicker-preview="#iconpicker-preview-<?php echo $id; ?>"><i class="fas fa-trash-alt"></i></button>
</div>
<input<?php echo $view->attributes; ?>>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo $id; ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
<?php
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        IconPicker.Init({
            jsonUrl: \'' . asset('js/iconpicker/iconpicker-1.5.0.json') . '\',
            searchPlaceholder: \'' . e(__('form.label.icon.search')) . '\',
            showAllButton: \'' . e(__('form.label.icon.show')) . '\',
            cancelButton: \'' . e(__('form.label.icon.cancel')) . '\',
            noResultsFound: \'' . e(__('form.label.icon.no_results')) . '\',
            borderRadius: \'0px\',
        });

        IconPicker.Run(\'#iconpicker-' . $id . '\');

        $(\'#iconpicker-' . $id . '-drop\').on(\'click\', function (event) {
            $($(this).data(\'iconpicker-preview\')).attr(\'class\', \'\');
            $(\'#' . $id . '\').val(\'\');
        });
    });
</script>');
?>
