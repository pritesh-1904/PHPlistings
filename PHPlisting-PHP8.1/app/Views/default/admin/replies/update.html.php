<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('manage/' . $view->type->slug, session()->get('admin/manage/' . $view->type->slug)); ?>"><?php echo e(__('admin.messages.breadcrumb.listings', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('manage/' . $view->type->slug . '/summary/' . $view->message->listing->id); ?>"><?php echo e(__('admin.messages.breadcrumb.listing', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'listing' => $view->message->listing->title])); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo adminRoute($view->type->slug . '-messages', session()->get('admin/' . $view->type->slug . '-messages')); ?>"><?php echo e(__('admin.messages.breadcrumb.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo adminRoute($view->type->slug . '-replies', ['message_id' => $view->message->id]); ?>"><?php echo e(__('admin.replies.breadcrumb.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'message' => $view->message->title])); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('admin.replies.breadcrumb.update', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'message' => $view->message->title])); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.replies.heading.update', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></h3>
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
