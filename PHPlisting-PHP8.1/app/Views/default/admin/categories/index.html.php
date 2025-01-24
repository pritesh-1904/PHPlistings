<?php if (isset($view->parent) && null !== $view->parent && false === $view->parent->isRoot()) { ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
<?php 
    foreach ($view->parent->ancestors()->get() as $ancestor) {
        echo '<li class="breadcrumb-item"><a href="' . adminRoute('categories/' . $view->type->slug, ['parent_id' => $ancestor->id]) . '">' . e($ancestor->name) . '</a></li>';
    }

    echo '<li class="breadcrumb-item active">' . e($view->parent->name) . '</li>';
?>
    </ol>
</nav>
<?php } ?>
<div class="mb-5">
    <h3><?php echo e(__('admin.categories.heading.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></h3>
</div>

<div class="clearfix mb-3">
    <a href="<?php echo adminRoute('categories/' . $view->type->slug . '/create'); ?>" class="btn btn-success btn-lg mb-3"><i class="fas fa-plus"></i> <?php echo e(__('admin.categories.button.create', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a>
    <a href="<?php echo adminRoute('categories/' . $view->type->slug . '/create-multiple'); ?>" class="btn btn-light btn-lg mb-3"><i class="fas fa-plus"></i> <?php echo e(__('admin.categories.button.create_multiple', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a>
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
                <?php echo $view->categories; ?>
            </div>
        </div>
    </div>
</div>
