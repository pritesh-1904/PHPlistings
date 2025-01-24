<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('widget-fields/' . $view->group->id, session()->get('admin/widget-fields/' . $view->group->id)); ?>"><?php echo __('admin.widgetfields.breadcrumb.index', ['group' => $view->group->name]); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('admin.widgetfieldconstraints.breadcrumb.index', ['group' => $view->group->name, 'field' => $view->field->label])); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.widgetfieldconstraints.heading.index', ['group' => $view->group->name, 'field' => $view->field->label])); ?></h3>
</div>
<a href="<?php echo adminRoute('widget-field-constraints/' . $view->group->id . '/create', ['field_id' => $view->field->id]); ?>" class="btn btn-success btn-lg mb-3"><i class="fas fa-plus"></i> <?php echo e(__('admin.widgetfieldconstraints.button.create', ['group' => $view->group->name, 'field' => $view->field->label])); ?></a>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo session('success', null); ?>
                <?php echo session('error', null); ?>
                <?php echo $view->constraints; ?>
            </div>
        </div>
    </div>
</div>
