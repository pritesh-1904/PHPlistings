<?php $view->attributes->append('class', 'btn btn-outline-secondary btn-lg'); ?>
<?php if (null !== $view->attributes->get('href')) { ?>
<a<?php echo $view->attributes; ?>><?php echo e($view->label); ?></a>
<?php } else { ?>
<?php $view->attributes->add('type', 'button'); ?>
<button<?php echo $view->attributes; ?>><?php echo e($view->label); ?></button>
<?php } ?>
