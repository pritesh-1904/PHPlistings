<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('widget-fields/' . $view->group->id, session()->get('admin/widget-fields/' . $view->group->id)); ?>"><?php echo __('admin.widgetfields.breadcrumb.index', ['group' => $view->group->name]); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('widget-field-options/' . $view->group->id, ['field_id' => $view->field->id]); ?>"><?php echo e(__('admin.widgetfieldoptions.breadcrumb.index', ['group' => $view->group->name, 'field' => $view->field->label])); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('admin.widgetfieldoptions.breadcrumb.create', ['group' => $view->group->name, 'field' => $view->field->label])); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.widgetfieldoptions.heading.create', ['group' => $view->group->name, 'field' => $view->field->label])); ?></h3>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo $alert ?? null ?>
                <?php echo $view->form; ?>
            </div>
        </div>
    </div>
</div>
