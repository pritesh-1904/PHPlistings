<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('menu-groups', session()->get('admin/menu-groups')); ?>"><?php echo e(__('admin.menugroups.breadcrumb.groups')); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('menu/' . $view->group->id, session()->get('admin/menu/' . $view->group->id)); ?>"><?php echo e(__('admin.menu.breadcrumb.items', ['group' => $view->group->name])); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('admin.menu.breadcrumb.create')); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.menu.heading.create', ['group' => $view->group->name])); ?></h3>
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
