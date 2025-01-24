<div class="mb-5">
    <h3><?php echo e(__('admin.listingfields.'. $view->group->slug . '.heading.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></h3>
</div>
<a href="<?php echo adminRoute($view->group->slug . '-fields/' . $view->type->slug . '/create'); ?>" class="btn btn-success btn-lg mb-3"><i class="fas fa-plus"></i> <?php echo e(__('admin.listingfields.'. $view->group->slug . '.button.create', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo session('success', null); ?>
                <?php echo session('error', null); ?>
                <?php echo $view->fields; ?>
            </div>
        </div>
    </div>
</div>
