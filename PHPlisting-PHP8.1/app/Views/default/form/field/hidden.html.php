<?php
    $view->attributes->add('name', e($view->name));
    $view->attributes->add('type', 'hidden');
    $view->attributes->add('value', $view->value);
?>
<input<?php echo $view->attributes; ?>>
<?php if (isset($view->sluggable) && null !== $view->sluggable) { ?>
<?php
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        $(\'#' . e($view->attributes->id) . '\').slug({
            \'url\': \'' . route('ajax/slugify') . '\',
            \'source\': \'' . e($view->sluggable) . '\',
        });
    });
</script>');
?>
<?php } ?>