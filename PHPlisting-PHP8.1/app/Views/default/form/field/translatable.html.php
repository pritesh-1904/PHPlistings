<?php
    $id = e($view->attributes->id);

    $value = $view->reverseTransform($view->value);

    $view->attributes->append('class', 'form-control');
    $view->attributes->add('type', 'text');

    if (isset($view->placeholder)) {
        $view->attributes->add('placeholder', e($view->placeholder));
    }

    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', e($view->attributes->id) . '-description');
    }

    if (count($view->errors) > 0) {
        $view->attributes->append('class', 'is-invalid');
    }

    foreach ($view->getConstraints() as $constraint) {
        if ($constraint instanceof \App\Src\Validation\TransmaxlengthValidator) {
            $view->attributes->add('maxlength', $constraint->getParameter());
        }
    }

    $locales = locale()->getSupportedWithOptions();

    if ($locales->count() > 1) {
        echo '<ul class="nav nav-pills btn-sm mb-2" id="' . $id . '" role="tablist">';

        $count = 1;

        foreach ($locales as $locale) {
            echo '<li class="nav-item"><a class="nav-link' . ($count == 1 ? ' active' : '') . '" id="' . $view->attributes->id . '-' . $locale->locale . '-tab" data-toggle="tab" href="#' . $view->attributes->id . '-' . $locale->locale . '-panel" role="tab" aria-controls="' . e($view->attributes->id) . '-' . e($locale->locale) . '-panel">' . e($locale->native) . '</a></li>';
            $count++;
        }

        echo '</ul><div class="tab-content">';

        $count = 1;

        foreach (locale()->getSupported() as $locale) {
            $view->attributes->id = $id . '-' . e($locale);
            $view->attributes->name = e($view->name) . '[' . e($locale) . ']';
            $view->attributes->value = (isset($value[$locale])) ? $value[$locale] : null;

?>
<div class="tab-pane fade<?php if ($count == 1) echo ' show active'; ?>" id="<?php echo $view->attributes->id; ?>-panel" role="tabpanel" aria-labelledby="<?php echo $view->attributes->id; ?>-tab">
    <input<?php echo $view->attributes; ?>>
    <?php if (false !== $view->attributes->has('maxlength')) { ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#<?php echo $view->attributes->id; ?>').count({
                'limit': '<?php echo (int) $view->attributes->maxlength; ?>',
                'template': '<?php echo e(__('form.label.characters_left')); ?>'
            });
        });
    </script>
    <small class="form-text text-muted" id="<?php echo $view->attributes->id; ?>-counter"></small>
    <?php } ?>
</div>

<?php $count++; } ?>
</div>
<?php } else if ($locales->count() == 1) {
    $locale = $locales->first()->locale;

    $view->attributes->add('id', $id . '-' . e($locale));
    $view->attributes->add('name', e($view->name) . '[' . e($locale) . ']');
    $view->attributes->add('value', (isset($value[$locale]) ? $value[$locale] : null));
?>
<input<?php echo $view->attributes; ?>>
<?php if (false !== $view->attributes->has('maxlength')) { ?>
    <?php
    layout()->addFooterJs('<script>
        $(document).ready(function() {
            $(\'#' . $view->attributes->id . '\').count({
                \'limit\': \'' . (int) $view->attributes->maxlength . '\',
                \'template\': \'' . e(__('form.label.characters_left')) . '\',
            });
        });
    </script>');
    ?>
    <small class="form-text text-muted" id="<?php echo $view->attributes->id; ?>-counter"></small>
<?php } ?>
<?php } ?>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo $id; ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
