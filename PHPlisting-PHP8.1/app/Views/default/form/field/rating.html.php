<?php 
    layout()->addCss('<link href="' . asset('js/rateit/rateit.css') . '" rel="stylesheet">');
    layout()->addJs('<script src="' . asset('js/rateit/jquery.rateit.min.js') . '"></script>');

    $view->attributes->add('name', e($view->name));
    $view->attributes->add('type', 'range');
    $view->attributes->add('min', '0');
    $view->attributes->add('max', '5');
    $view->attributes->add('step', '0.5');
    $view->attributes->add('value', e($view->value ?? '0'));
?>
<input<?php echo $view->attributes; ?>>
<div class="rateit svg pt-2" style="width: 100px; height: 35px;"
    data-rateit-resetable="false"
    data-rateit-starwidth="19"
    data-rateit-starheight="19"
    data-rateit-backingfld="#<?php echo e($view->attributes->id); ?>"
>
</div>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
