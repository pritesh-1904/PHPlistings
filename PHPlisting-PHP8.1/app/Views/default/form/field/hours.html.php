<?php
    $view->attributes->append('class', 'form-control');
    $view->attributes->add('name', e($view->name));
    $view->attributes->add('type', 'hidden');
    $view->attributes->add('value', $view->value);

    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', e($view->attributes->id) . '-description');
    }
    if (count($view->errors) > 0) {
        $view->attributes->append('class', 'is-invalid');
    }
?>
<div class="border rounded p-2">
    <div id="<?php echo e($view->attributes->id); ?>-container" class="opening-hours"></div>
    <div id="<?php echo e($view->attributes->id); ?>-controls" class="bg-light p-2">
        <div class="row">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-6 col-md-3 my-1">
                        <?php echo (new \App\Src\Form\Type\Select(e($view->attributes->id) . '-dow', ['options' => locale()->getDaysOfWeek()]))->render(); ?>
                    </div>
                    <div class="col-6 col-md-3 my-1">
                        <?php echo (new \App\Src\Form\Type\Time(e($view->attributes->id) . '-start', ['placeholder' => e(__('hour.form.placeholder.from'))]))->render(); ?>
                    </div>
                    <div class="col-6 col-md-3 my-1">
                        <?php echo (new \App\Src\Form\Type\Time(e($view->attributes->id) . '-end', ['placeholder' => e(__('hour.form.placeholder.to'))]))->render(); ?>
                    </div>
                    <div class="col-6 col-md-3 my-1 d-md-flex align-items-md-center">
                        <?php echo (new \App\Src\Form\Type\Button(e($view->attributes->id) . '-save', ['label' => e(__('hour.form.label.save'))]))->render(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input<?php echo $view->attributes; ?>>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
<?php 
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        $(\'#' . e($view->attributes->id) . '\').hours({
            \'url\': \'' . route('ajax/hours') . '\',
        });
    });
</script>');
?>
