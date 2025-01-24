<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('email-templates', session()->get('admin/email-templates')); ?>"><?php echo e(__('admin.emailtemplates.breadcrumb.templates')); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo e($view->template->name); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.emailtemplates.heading.update')); ?></h3>
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
