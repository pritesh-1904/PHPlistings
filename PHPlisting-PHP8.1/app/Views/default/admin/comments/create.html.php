<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('manage/' . $view->type->slug, session()->get('admin/manage/' . $view->type->slug)); ?>"><?php echo e(__('admin.reviews.breadcrumb.listings', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('manage/' . $view->type->slug . '/summary/' . $view->review->listing->id); ?>"><?php echo e(__('admin.reviews.breadcrumb.listing', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'listing' => $view->review->listing->title])); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo adminRoute($view->type->slug . '-reviews', ['listing_id' => $view->review->listing->id]); ?>"><?php echo e(__('admin.reviews.breadcrumb.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo adminRoute($view->type->slug . '-comments', ['review_id' => $view->review->id]); ?>"><?php echo e(__('admin.comments.breadcrumb.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'review' => $view->review->title])); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('admin.comments.breadcrumb.create', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'review' => $view->review->title])); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.comments.heading.create', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></h3>
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
