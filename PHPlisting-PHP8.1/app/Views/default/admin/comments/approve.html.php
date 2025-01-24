<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute($view->type->slug . '-reviews'); ?>"><?php echo e(__('admin.reviews.breadcrumb.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('admin.comments.breadcrumb.approve', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.comments.heading.approve', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></h3>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo $view->alert ?? null; ?>
                <?php echo session('success', null); ?>
                <?php echo session('error', null); ?>
                <?php echo $view->comments; ?>
            </div>
        </div>
    </div>
</div>
