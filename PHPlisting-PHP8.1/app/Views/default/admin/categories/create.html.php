<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('categories/' . $view->type->slug, session()->get('admin/categories/' . $view->type->slug)); ?>"><?php echo e(__('admin.categories.breadcrumb.categories', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('admin.categories.breadcrumb.create', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.categories.heading.create', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></h3>
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
