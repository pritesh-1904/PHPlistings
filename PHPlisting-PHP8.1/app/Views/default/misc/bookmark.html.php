<?php if (null !== $view->get('type') && 'button' == $view->type) { ?>
    <?php if (null !== $view->get('state') && $view->state == 'on') { ?>
        <i class="fas fa-bookmark"></i> <?php echo e(__('bookmark.unbookmark')); ?>
    <?php } else { ?>
        <i class="far fa-bookmark"></i> <?php echo e(__('bookmark.bookmark')); ?>
    <?php } ?>
<?php } else { ?>
    <?php if (null !== $view->get('state') && $view->state == 'on') { ?>
        <i class="fas fa-bookmark text-secondary display-9" title="<?php echo e(__('bookmark.unbookmark')); ?>"></i>
    <?php } else { ?>
        <i class="far fa-bookmark text-secondary display-9" title="<?php echo e(__('bookmark.bookmark')); ?>"></i>
    <?php } ?>
<?php } ?>