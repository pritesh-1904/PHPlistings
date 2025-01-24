<div class="mb-5">
    <h3><?php echo e(__('admin.uploadtypes.heading.index')); ?></h3>
</div>
<a href="<?php echo adminRoute('upload-types/create'); ?>" class="btn btn-success btn-lg mb-3"><i class="fas fa-plus"></i> <?php echo e(__('admin.uploadtypes.button.create')); ?></a>
<a href="<?php echo adminRoute('file-types'); ?>" class="btn btn-info btn-lg mb-3"><i class="fas fa-edit"></i> <?php echo e(__('admin.uploadtypes.button.filetypes')); ?></a>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo session('success', null); ?>
                <?php echo session('error', null); ?>
                <?php echo $view->types; ?>
            </div>
        </div>
    </div>
</div>
