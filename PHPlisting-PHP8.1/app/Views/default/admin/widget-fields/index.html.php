<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('widget-field-groups', session()->get('admin/widget-field-groups')); ?>"><?php echo __('admin.widgetfieldgroups.breadcrumb.index'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo e(__('admin.widgetfields.breadcrumb.index', ['group' => $view->group->name])); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.widgetfields.heading.index', ['group' => $view->group->name])); ?></h3>
</div>
<a href="<?php echo adminRoute('widget-fields/' . $view->group->id . '/create'); ?>" class="btn btn-success btn-lg mb-3"><i class="fas fa-plus"></i> <?php echo e(__('admin.widgetfields.button.create')); ?></a>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo session('success', null); ?>
                <?php echo session('error', null); ?>
                <?php echo $view->fields; ?>
            </div>
        </div>
    </div>
</div>
