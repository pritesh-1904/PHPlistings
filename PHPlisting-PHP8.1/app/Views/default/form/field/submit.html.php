<?php
    $view->attributes->append('class', 'btn btn-primary btn-lg');
    $view->attributes->add('type', 'submit');
    $view->attributes->add('name', e($view->name));
?>
<button<?php echo $view->attributes; ?>><?php echo e($view->label ?? e(__('form.label.submit'))); ?></button>
