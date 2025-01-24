<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('widget-fields/' . $view->group->id, session()->get('admin/widget-fields/' . $view->group->id)); ?>"><?php echo __('admin.widgetfields.breadcrumb.index', ['group' => $view->group->name]); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo __('admin.widgetfields.breadcrumb.update', ['group' => $view->group->name]); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.widgetfields.heading.update', ['group' => $view->group->name])); ?></h3>
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
