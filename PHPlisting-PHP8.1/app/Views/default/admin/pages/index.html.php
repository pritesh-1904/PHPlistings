<div class="mb-5">
    <h3><?php echo e(__('admin.pages.heading.index')); ?></h3>
</div>
<div class="clearfix mb-3">
    <a href="<?php echo adminRoute('pages/create'); ?>" class="btn btn-success btn-lg mb-3 float-left"><i class="fas fa-plus"></i> <?php echo e(__('admin.pages.button.create')); ?></a>
    <div class="float-right">
        <?php echo $view->form; ?>
    </div>
</div>
<div class="row ">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo session('success', null); ?>
                <?php echo session('error', null); ?>
                <?php echo $view->pages; ?>
            </div>
        </div>
    </div>
</div>
