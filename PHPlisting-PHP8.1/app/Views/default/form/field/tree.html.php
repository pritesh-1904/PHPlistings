<?php 
    layout()->addCss('<link href="' . asset('js/fancytree/skin-lion/ui.fancytree.min.css') . '" rel="stylesheet">');
    layout()->addJs('<script src="' . asset('js/fancytree/jquery.fancytree-all-deps.min.js') . '"></script>');

    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', e($view->attributes->id) . '-description');
    }

    foreach($view->getConstraints() as $constraint) {
        if ($constraint instanceof \App\Src\Validation\MaxlengthValidator) {
            $view->attributes->add('maxlength', $constraint->getParameter());
        }
    }
?>
<div class="input-group input-group-sm mb-2">
    <input type="text" id="<?php echo e($view->attributes->id); ?>-tree-search" placeholder="<?php echo e(__('form.label.filter')); ?>" autocomplete="off" class="form-control">
    <div class="input-group-append" id="<?php echo e($view->attributes->id); ?>-tree-search-clear">
        <span class="input-group-text"><i class="fas fa-times"></i></span>
    </div>
</div>
<div id="<?php echo e($view->attributes->id); ?>"></div>

<?php
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        $(\'#' . e($view->attributes->id) . '\').tree({
            \'source\': ' . json_encode($view->tree_source) . ',
            \'value\': \'' . json_encode((array) $view->value) . '\',
            \'limit\': \'' . ($view->attributes->maxlength ?? 0) . '\',
            \'leaves\': ' . (isset($view->tree_leaves) && false === $view->tree_leaves ? 'false' : 'true') . ',
            \'rtl\': ' . (locale()->getDirection() == 'rtl' ? 'true' : 'false') . ',
        });
    });
</script>');
?>
<?php if (false !== $view->attributes->has('maxlength') && $view->attributes->maxlength > 0) { ?>
    <small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-counter"></small>
<?php } ?>

<?php if (false === $view->attributes->has('maxlength') || $view->attributes->maxlength == 0) { ?>
<div class="mt-2">
    <span class="btn btn-light btn-sm" id="<?php echo e($view->attributes->id); ?>-select-all"><?php echo e(__('form.label.select_all')); ?></span>
    <span class="btn btn-light btn-sm" id="<?php echo e($view->attributes->id); ?>-deselect-all"><?php echo e(__('form.label.deselect_all')); ?></span>
</div>
<?php } ?>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo $id; ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
