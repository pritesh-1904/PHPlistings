<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute($view->group->slug . '-fields/' . $view->type->slug, session()->get('admin/' . $view->group->slug . '-fields/' . $view->type->slug)); ?>"><?php echo e(__('admin.listingfields.'. $view->group->slug . '.breadcrumb.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('admin.listingfieldconstraints.' . $view->group->slug . '.breadcrumb.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'field' => $view->field->label])); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.listingfieldconstraints.' . $view->group->slug . '.heading.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></h3>
</div>
<a href="<?php echo adminRoute($view->group->slug . '-field-constraints/' . $view->type->slug . '/create', ['field_id' => $view->field->id]); ?>" class="btn btn-success btn-lg mb-3"><i class="fas fa-plus"></i> <?php echo e(__('admin.listingfieldconstraints.'. $view->group->slug . '.button.create', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a>
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
