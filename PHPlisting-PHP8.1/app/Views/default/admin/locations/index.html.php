<?php if (isset($view->parent) && null !== $view->parent && false === $view->parent->isRoot()) { ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
<?php 
    foreach ($view->parent->ancestors()->get() as $ancestor) {
        echo '<li class="breadcrumb-item"><a href="' . adminRoute('locations', ['parent_id' => $ancestor->id]) . '">' . e($ancestor->name) . '</a></li>';
    }

    echo '<li class="breadcrumb-item active">' . e($view->parent->name) . '</li>';
?>
    </ol>
</nav>
<?php } ?>
<div class="mb-5">
    <h3><?php echo e(__('admin.locations.heading.index')); ?></h3>
</div>

<div class="clearfix mb-3">
    <a href="<?php echo adminRoute('locations/create'); ?>" class="btn btn-success btn-lg mb-3"><i class="fas fa-plus"></i> <?php echo e(__('admin.locations.button.create')); ?></a>
    <a href="<?php echo adminRoute('locations/create-multiple'); ?>" class="btn btn-light btn-lg mb-3"><i class="fas fa-plus"></i> <?php echo e(__('admin.locations.button.create_multiple')); ?></a>
    <div class="float-right">
        <?php echo $view->form; ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo session('success', null); ?>
                <?php echo session('error', null); ?>
                <?php echo $view->locations; ?>
            </div>
        </div>
    </div>
</div>
