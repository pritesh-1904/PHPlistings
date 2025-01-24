<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('menu-groups', session()->get('admin/menu-groups')); ?>"><?php echo e(__('admin.menugroups.breadcrumb.groups')); ?></a></li>
<?php 
    if (isset($view->parent) && null !== $view->parent) {
        echo '<li class="breadcrumb-item"><a href="' . adminRoute('menu/' . $view->group->id) . '">' . $view->group->name . '</a></li>';
        echo '<li class="breadcrumb-item active" aria-current="page">' . $view->parent->name . '</li>';
    } else {
        echo '<li class="breadcrumb-item active" aria-current="page">' . $view->group->name . '</li>';
    }
?>        
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.menu.heading.index', ['group' => $view->group->name])); ?></h3>
</div>
<a href="<?php echo adminRoute('menu/' . $view->group->id . '/create'); ?>" class="btn btn-success btn-lg mb-3"><i class="fas fa-plus"></i> <?php echo e(__('admin.menu.button.create', ['group' => $view->group->name])); ?></a>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo session('success', null); ?>
                <?php echo session('error', null); ?>
                <?php echo $view->menuItems; ?>
            </div>
        </div>
    </div>
</div>
