<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('products/' . $view->type->slug, session()->get('admin/products/' . $view->type->slug)); ?>"><?php echo __('admin.products.breadcrumb.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural]); ?></a></li>
        <li class="breadcrumb-item active"><?php echo e(__('admin.pricings.breadcrumb.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'product' => $view->product->name])); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.pricings.heading.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'product' => $view->product->name])); ?></h3>
</div>
<a href="<?php echo adminRoute('pricings/' . $view->type->slug . '/create', ['product_id' => $view->product->id]); ?>" class="btn btn-success btn-lg mb-3"><i class="fas fa-plus"></i> <?php echo e(__('admin.pricings.button.create', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'product' => $view->product->name])); ?></a>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo session('success', null); ?>
                <?php echo session('error', null); ?>
                <?php echo $view->pricings; ?>
            </div>
        </div>
    </div>
</div>
