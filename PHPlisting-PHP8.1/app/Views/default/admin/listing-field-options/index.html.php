<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute($view->group->slug . '-fields/' . $view->type->slug, session()->get('admin/' . $view->group->slug . '-fields/' . $view->type->slug)); ?>"><?php echo e(__('admin.listingfields.'. $view->group->slug . '.breadcrumb.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'field' => $view->field->label])); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('admin.listingfieldoptions.' . $view->group->slug . '.breadcrumb.index', ['field' => $view->field->label, 'singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.listingfieldoptions.' . $view->group->slug . '.heading.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></h3>
</div>
<?php if (null !== $view->field->get('customizable')) { ?>
    <a href="<?php echo adminRoute($view->group->slug . '-field-options/' . $view->type->slug . '/create', ['field_id' => $view->field->id]); ?>" class="btn btn-success btn-lg mb-3"><i class="fas fa-plus"></i> <?php echo e(__('admin.listingfieldoptions.'. $view->group->slug . '.button.create', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a>
    <a href="<?php echo adminRoute($view->group->slug . '-field-options/' . $view->type->slug . '/create-multiple', ['field_id' => $view->field->id]); ?>" class="btn btn-light btn-lg mb-3"><i class="fas fa-plus"></i> <?php echo e(__('admin.listingfieldoptions.'. $view->group->slug . '.button.create_multiple', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a>
<?php } ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo session('success', null); ?>
                <?php echo session('error', null); ?>
                <?php echo $view->options; ?>
            </div>
        </div>
    </div>
</div>
