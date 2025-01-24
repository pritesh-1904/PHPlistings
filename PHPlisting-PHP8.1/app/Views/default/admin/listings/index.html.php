<?php if (null !== request()->get->get('user_id') && null !== $user = \App\Models\User::find(request()->get->get('user_id'))) { ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('users', session()->get('admin/users')); ?>"><?php echo e(__('admin.users.breadcrumb.index')); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('users/summary/' . $user->id); ?>"><?php echo e(__('admin.users.breadcrumb.summary', ['user' => $user->getNameWithId()])); ?></a></li>
        <li class="breadcrumb-item active"><?php echo e(__('admin.listings.breadcrumb.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></li>
    </ol>
</nav>
<?php } ?>
<div class="mb-5">
    <h3><?php echo e(__('admin.listings.heading.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></h3>
</div>
<div class="clearfix mb-3">
    <a href="<?php echo adminRoute('manage/' . $view->type->slug . '/create'); ?>" class="btn btn-success btn-lg mb-3"><i class="fas fa-plus"></i> <?php echo e(__('admin.listings.button.create', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a>
    <div class="row">
            <div class="col-lg-12">
            <div class="card border-0 rounded-0 shadow-md px-3 pt-2">
                <div class="card-body">
                    <?php echo $view->form; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <?php echo session('success', null); ?>
                <?php echo session('error', null); ?>
                <?php echo $view->alert ?? null; ?>
                <?php echo $view->listings; ?>
            </div>
        </div>
    </div>
</div>
