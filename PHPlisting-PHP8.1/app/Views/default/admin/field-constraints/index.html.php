<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('fields/' . $view->group->slug, session()->get('admin/fields/' . $view->group->slug)); ?>"><?php echo e(__('admin.fields.'. $view->group->slug . '.breadcrumb.index', ['field' => $view->field->label])); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('admin.fieldconstraints.'. $view->group->slug . '.breadcrumb.index', ['field' => $view->field->label])); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.fieldconstraints.' . $view->group->slug . '.heading.index')); ?></h3>
</div>
<a href="<?php echo adminRoute('field-constraints/' . $view->group->slug . '/create', ['field_id' => $view->field->id]); ?>" class="btn btn-success btn-lg mb-3"><i class="fas fa-plus"></i> <?php echo e(__('admin.fieldconstraints.'. $view->group->slug . '.button.create')); ?></a>
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
