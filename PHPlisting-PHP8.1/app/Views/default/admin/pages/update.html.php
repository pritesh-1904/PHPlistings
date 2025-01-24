<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('pages', session()->get('admin/pages')); ?>"><?php echo e(__('admin.pages.breadcrumb.index')); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('widgets/' . $view->page->id, session()->get('admin/widgets/' . $view->page->id)); ?>"><?php echo e(__('admin.widgets.breadcrumb.index', ['page' => $view->page->title, 'slug' => $view->page->slug])); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo __('admin.pages.breadcrumb.update'); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.pages.heading.update')); ?></h3>
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
