<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('manage/' . $view->type->slug, session()->get('admin/manage/' . $view->type->slug)); ?>"><?php echo e(__('admin.claims.breadcrumb.listings', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a></li>
        <?php if (null !== request()->get->get('listing_id') && '' != request()->get->get('listing_id')) { ?>
            <li class="breadcrumb-item"><a href="<?php echo adminRoute('manage/' . $view->type->slug . '/summary/' . request()->get->get('listing_id')); ?>"><?php echo e(__('admin.claims.breadcrumb.listing', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'listing' => $view->listing->title])); ?></a></li>
        <?php } ?>
        <li class="breadcrumb-item active"><?php echo e(__('admin.claims.breadcrumb.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.claims.heading.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></h3>
</div>
<div class="mb-3">
    <?php echo $view->form; ?>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo session('success', null); ?>
                <?php echo session('error', null); ?>
                <?php echo $view->claims; ?>
            </div>
        </div>
    </div>
</div>
