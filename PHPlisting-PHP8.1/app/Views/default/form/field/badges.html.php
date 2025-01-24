<?php
    $view->attributes->append('class', 'custom-control-input');
    $view->attributes->add('type', 'checkbox');
    $view->attributes->add('name', e($view->name) . '[]');

    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', e($view->attributes->id) . '-description');
    }

    if (count($view->errors) > 0) {
        $view->attributes->append('class', 'is-invalid');
    }

    $count = 1;
    $id = e($view->attributes->id);
?>
<div class="border rounded p-3">
<?php
    foreach ((array) ($view->getOptions() ?? []) as $value => $badge) {
        $view->attributes->add('checked', false);
        $view->attributes->add('value', $value);

        if (in_array($value, $view->value ?? [])) {
            $view->attributes->add('checked', true);
        }

        $view->attributes->add('id', $id . '_' . $count);
?>
<div class="custom-control-lg custom-control custom-control-inline custom-checkbox">
    <div class="p-1">
        <input<?php echo $view->attributes; ?>>
        <label class="custom-control-label" for="<?php echo e($view->attributes->id); ?>"><?php echo view('misc/badge', ['badge' => $badge]); ?></label>
    </div>
</div>
<?php 
        $count++;
    }
?>
</div>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
