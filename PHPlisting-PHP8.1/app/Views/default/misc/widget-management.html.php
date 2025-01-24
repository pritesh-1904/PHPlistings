<div class="widget-management-wrapper">
    <?php echo $view->content; ?>
    <div class="widget-management text-right p-3">
        <a class="btn btn-dark btn-sm" target="_blank" href="<?php echo adminRoute('widgets/' . $view->page->id . '/update/' . $view->widget->getId()); ?>" title="<?php echo e(__('toolbar.label.manage_widget')); ?>"><i class="fas fa-edit"></i></a>
    </div>
</div>