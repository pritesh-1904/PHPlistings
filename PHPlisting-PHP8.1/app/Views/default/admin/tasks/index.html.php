<div class="mb-5">
    <h3><?php echo e(__('admin.tasks.heading.index')); ?></h3>
</div>
<a href="<?php echo adminRoute('tasks'); ?>" class="btn btn-success btn-lg mb-3"><i class="fas fa-sync-alt"></i> <?php echo e(__('admin.tasks.button.refresh')); ?></a>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo $view->alert ?? null; ?>
                <?php echo $view->tasks; ?>
            </div>
        </div>
    </div>
</div>
