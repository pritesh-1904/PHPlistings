<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('products/' . $view->type->slug, session()->get('admin/products/' . $view->type->slug)); ?>"><?php echo __('admin.products.breadcrumb.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural]); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('pricings/' . $view->type->slug, ['product_id' => $view->product->id]); ?>"><?php echo __('admin.pricings.breadcrumb.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'product' => $view->product->name]); ?></a></li>
        <li class="breadcrumb-item active"><?php echo e(__('admin.pricings.breadcrumb.update', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'product' => $view->product->name])); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.pricings.heading.update', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'product' => $view->product->name])); ?></h3>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-md border-0 rounded-0 p-3">
            <div class="card-body">
                <?php echo $view->alert ?? null; ?>
                <?php echo $view->form; ?>
            </div>
        </div>
    </div>
</div>
