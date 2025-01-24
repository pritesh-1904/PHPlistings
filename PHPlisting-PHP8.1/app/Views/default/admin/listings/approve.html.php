<div class="mb-5">
    <h3><?php echo e(__('admin.listings.heading.approve', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></h3>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo session('success', null); ?>
                <?php echo session('error', null); ?>
                <?php echo $view->alert ?? null; ?>
                <?php echo $view->listings; ?>
            </div>
        </div>
    </div>
</div>
