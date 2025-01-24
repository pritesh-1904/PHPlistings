<div class="mb-5">
    <h3><?php echo e(__('admin.index.title.dashboard')); ?></h3>
</div>
<?php echo session('success', null); ?>
<?php echo session('error', null); ?>
<div class="row">
    <div class="col-12">
        <div class="row">

            <?php if (($cronjobs = db()->table('cronjobs')->where(db()->raw('HOUR(TIMEDIFF(NOW(), last_run_datetime)) > 24'))->whereNotNull('locked')->count()) > 0) { ?>
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div class="card border-0 rounded-0 shadow-md py-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card-text">
                                <h5 class="display-6 text-medium mb-2"><?php echo $cronjobs; ?></h5>
                                <p class="text-secondary m-0"><?php echo e(__('admin.dashboard.label.tasks')); ?></p>
                            </div>
                            <div class="card-icon">
                                <i class="fas fa-exclamation text-danger display-3"></i>
                            </div>
                        </div>
                        <a href="<?php echo adminRoute('tasks'); ?>" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            <?php } ?>
            
            <?php if (false !== auth()->check(['admin_content', 'admin_listings'])) { ?>
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div class="card border-0 rounded-0 shadow-md py-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card-text">
                                <h5 class="display-6 text-medium mb-2"><?php echo locale()->formatPrice($view->revenue ?? 0); ?></h5>
                                <p class="text-secondary m-0"><?php echo e(__('admin.dashboard.label.revenue')); ?></p>
                            </div>
                            <div class="card-icon">
                                <i class="fas fa-dollar-sign text-primary display-3"></i>
                            </div>
                        </div>
                        <!-- <a href="#" class="stretched-link"></a> -->
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (false !== auth()->check('admin_users')) { ?>
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div class="card border-0 rounded-0 shadow-md py-3">
                    <?php if ($view->usersapprove > 0) { ?>
                        <div class="badge badge-pill badge-danger badge-notification display-10"><?php echo $view->usersapprove; ?></div>
                    <?php } ?>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card-text">
                                <h5 class="display-6 text-medium mb-2"><?php echo $view->users; ?></h5>
                                <p class="text-secondary m-0"><?php echo e(__('admin.dashboard.label.users')); ?></p>
                            </div>
                            <div class="card-icon">
                                <i class="fas fa-users text-primary display-3"></i>
                            </div>
                        </div>
                        <a href="<?php echo adminRoute('users'); ?>" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (false !== auth()->check(['admin_content', 'admin_listings'])) { ?>
                <?php foreach ($view->types as $type) { ?>

                <?php $total = $view->listings->where('type_id', $type->id)->first(); ?>
                <?php $approve = $view->listingsapprove->where('type_id', $type->id)->first(); ?>
                <div class="col-12 col-md-6 col-lg-3 mb-4">
                    <div class="card border-0 rounded-0 shadow-md py-3">
                        <?php if (null !== $approve) { ?>
                            <div class="badge badge-pill badge-danger badge-notification display-10"><?php echo $approve->get('total', '0'); ?></div>
                        <?php } ?>
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="card-text">
                                    <h5 class="display-6 text-medium mb-2"><?php echo (null !== $total) ? $total->get('total', '0') : '0'; ?></h5>
                                    <p class="text-secondary m-0"><?php echo e(__('admin.dashboard.label.total', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?></p>
                                </div>
                                <div class="card-icon">
                                    <i class="<?php echo $type->icon; ?> text-primary display-3"></i>
                                </div>
                            </div>
                            <a href="<?php echo adminRoute('manage/' . $type->slug); ?>" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
                <?php } ?>
            <?php } ?>
            <?php if (false !== auth()->check('admin_locations')) { ?>
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div class="card border-0 rounded-0 shadow-md py-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card-text">
                                <h5 class="display-6 text-medium mb-2"><?php echo $view->locations; ?></h5>
                                <p class="text-secondary m-0"><?php echo e(__('admin.dashboard.label.locations')); ?></p>
                            </div>
                            <div class="card-icon">
                                <i class="fas fa-map-marker-alt text-primary display-3"></i>
                            </div>
                        </div>
                        <a href="<?php echo adminRoute('locations'); ?>" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>    
    </div>
</div>
