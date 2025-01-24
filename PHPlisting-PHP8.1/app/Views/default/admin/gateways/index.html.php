<div class="mb-5">
    <h3><?php echo e(__('admin.gateways.heading.index')); ?></h3>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo session('success', null); ?>
                <?php echo session('error', null); ?>
                <?php echo $view->gateways; ?>
            </div>
        </div>
    </div>
</div>
