<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('users', session()->get('admin/users')); ?>"><?php echo e(__('admin.users.breadcrumb.index')); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('admin.users.breadcrumb.summary', ['user' => $view->user->getNameWithId()])); ?></li>
    </ol>
</nav>
<div class="mb-4">
    <h3 class="mb-3"><?php echo e($view->user->getName()); ?></h3>
    <a href="<?php echo adminRoute('users/update/' . $view->user->id); ?>" class="btn btn-round mb-sm-2 btn-light" role="button"><?php echo __('admin.users.button.edit'); ?></a>
    <?php if (false !== auth()->check(['admin_content', 'admin_listings'])) { ?>
        <?php foreach (\App\Models\Type::orderBy('weight')->get() as $type) { ?>
            <a href="<?php echo adminRoute('manage/' . $type->slug, ['user_id' => $view->user->id]); ?>" class="btn btn-round mb-sm-2 btn-light" role="button"><?php echo __('admin.users.button.type', ['singular' => $type->name_singular, 'plural' => $type->name_plural, 'count' => '<span class="badge badge-secondary">' . $view->user->listings()->where('type_id', $type->id)->count() . '</span>']); ?></a>
        <?php } ?>
    <?php } ?>
    <?php if (false !== auth()->check('admin_emails')) { ?>
        <a href="<?php echo adminRoute('email-queue', ['recipient_id' => $view->user->id]); ?>" class="btn btn-round mb-sm-2 btn-light" role="button"><?php echo __('admin.users.button.emails', ['count' => '<span class="badge badge-secondary">' . $view->user->emails()->count() . '</span>']); ?></a>
    <?php } ?>
    <?php if ('1' != $view->user->id) { ?>
        <a href="<?php echo adminRoute('users/delete/' . $view->user->id); ?>" data-toggle="confirmation" data-title="<?php echo e(__('default.confirmation.message')); ?>" data-btn-ok-label="<?php echo e(__('default.confirmation.yes')); ?>" data-btn-cancel-label="<?php echo e(__('default.confirmation.no')); ?>" class="btn btn-round mb-sm-2 btn-danger" role="button"><?php echo __('admin.users.button.delete'); ?></a>
    <?php } ?>
</div>
<div class="row">
    <div class="col-12 col-lg-6 mb-4">
        <div class="card h-100 shadow-md border-0 rounded-0">
            <div class="card-header d-flex align-items-center justify-content-between p-4 border-0">
                <h4 class="text-bold display-8"><?php echo __('admin.users.summary.heading.details'); ?></h4>
                <?php
                    echo view('misc/status', [
                        'type' => 'user',
                        'status' => $view->user->active,
                    ]);
                ?>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th class="border-0" scope="row"><?php echo __('admin.users.label.id'); ?></th>
                                <td class="border-0 text-right"><?php echo e($view->user->id); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('admin.users.label.group'); ?></th>
                                <td class="text-right"><?php echo e($view->user->account->group->name); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('admin.users.label.balance'); ?></th>
                                <td class="text-right"><?php echo locale()->formatPrice($view->user->account->balance); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('admin.users.label.date'); ?></th>
                                <td class="text-right"><?php echo locale()->formatDatetime($view->user->added_datetime, auth()->user()->timezone); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('admin.users.label.updated_date'); ?></th>
                                <td class="text-right"><?php echo locale()->formatDatetimeDiff($view->user->updated_datetime); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('admin.users.label.last_seen'); ?></th>
                                <td class="text-right"><?php echo locale()->formatDatetimeDiff($view->user->account->last_activity_datetime); ?> <span class="text-muted display-11">(IP: <?php echo $view->user->account->get('ip', 'n/a'); ?>)</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
