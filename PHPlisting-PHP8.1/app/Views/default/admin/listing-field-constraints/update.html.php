<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute($view->group->slug . '-field-constraints/' . $view->type->slug, ['field_id' => $view->field->id]); ?>"><?php echo e(__('admin.listingfieldconstraints.'. $view->group->slug . '.breadcrumb.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'field' => $view->field->label])); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo __('admin.listingfieldconstraints.' . $view->group->slug . '.breadcrumb.update', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural]); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.listingfieldconstraints.' . $view->group->slug . '.heading.update', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></h3>
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
