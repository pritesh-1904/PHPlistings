<div class="mb-5">
    <h3><?php echo e(__('admin.files.heading.index')); ?></h3>
</div>
<div class="mb-3">
    <?php echo $view->form; ?>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo session('success', null); ?>
                <?php echo session('error', null); ?>
                <?php echo $view->alert ?? null; ?>
                <?php echo $view->files; ?>
            </div>
        </div>
    </div>
</div>
