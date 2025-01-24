<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('pages', session()->get('admin/pages')); ?>"><?php echo e(__('admin.pages.breadcrumb.index')); ?></a></li>
        <li class="breadcrumb-item active"><?php echo e(__('admin.widgets.breadcrumb.index', ['page' => $view->page->title, 'slug' => $view->page->slug])); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.widgets.heading.index', ['page' => $view->page->title, 'slug' => $view->page->slug])); ?></h3>
</div>
<div class="clearfix mb-3">
    <a href="<?php echo adminRoute('pages/update/' . $view->page->id); ?>" class="btn btn-info btn-lg mb-3"><i class="fas fa-edit"></i> <?php echo e(__('admin.pages.button.options')); ?></a>
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
                <?php echo $view->widgets; ?>
            </div>
        </div>
    </div>
</div>
