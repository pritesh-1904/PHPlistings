<?php if (null !== request()->get->get('recipient_id') && null !== $user = \App\Models\User::find(request()->get->get('recipient_id'))) { ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('users', session()->get('admin/users')); ?>"><?php echo e(__('admin.users.breadcrumb.index')); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('users/summary/' . $user->id); ?>"><?php echo e(__('admin.users.breadcrumb.summary', ['user' => $user->getNameWithId()])); ?></a></li>
        <li class="breadcrumb-item active"><?php echo e(__('admin.emailqueue.breadcrumb.index')); ?></li>
    </ol>
</nav>
<?php } ?>
<div class="mb-5">
    <h3><?php echo e(__('admin.emailqueue.heading.index')); ?></h3>
</div>
<div class="mb-3">
    <?php echo $view->form; ?>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo $view->alert ?? null; ?>
                <?php echo $view->emails; ?>
            </div>
        </div>
    </div>
</div>
