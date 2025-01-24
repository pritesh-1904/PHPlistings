<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('manage/' . $view->type->slug, session()->get('admin/manage/' . $view->type->slug)); ?>"><?php echo e(__('admin.messages.breadcrumb.listings', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a></li>
        <?php if (null !== request()->get->get('listing_id') && '' != request()->get->get('listing_id')) { ?>
            <li class="breadcrumb-item"><a href="<?php echo adminRoute('manage/' . $view->type->slug . '/summary/' . request()->get->get('listing_id')); ?>"><?php echo e(__('admin.messages.breadcrumb.listing', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'listing' => $view->listing->title])); ?></a></li>
        <?php } ?>
        <li class="breadcrumb-item active"><?php echo e(__('admin.messages.breadcrumb.approve', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.messages.heading.approve', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></h3>
</div>
<div class="clearfix mb-3">
    <a href="<?php echo adminRoute($view->type->slug . '-messages/create'); ?>" class="btn btn-success btn-lg mb-3 float-left"><i class="fas fa-plus"></i> <?php echo e(__('admin.messages.button.create', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a>
    <div class="float-right">
        <?php echo $view->form; ?>
    </div>
</div>
<div class="row ">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo $view->alert ?? null; ?>
                <?php echo session('success', null); ?>
                <?php echo session('error', null); ?>
                <?php echo $view->messages; ?>
            </div>
        </div>
    </div>
</div>
