<nav class="navbar navbar-expand-md navbar-dark bg-dark py-0">
    <button class="navbar-toggler border-0 text-light display-10" type="button" data-toggle="collapse"
        data-target="#system_toolbar" aria-controls="system_toolbar" aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="system_toolbar">
        <ul class="navbar-nav display-11 text-thin mr-auto">
            <li class="nav-item active">
                <a class="nav-link px-lg-2 text-light" href="<?php echo adminRoute(''); ?>">
                    <i class="fas fa-tachometer-alt pr-1"></i> <?php echo e(__('toolbar.label.dashboard')); ?>
                </a>
            </li>
            <?php if (false !== auth()->check('admin_appearance')) { ?>
            <li class="nav-item">
                <a class="nav-link px-lg-2 text-light" href="<?php echo adminRoute('themes/update/' . theme()->get('slug')); ?>">
                    <i class="fas fa-paint-brush pr-1"></i> <?php echo e(__('toolbar.label.edit_theme')); ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-lg-2 text-light" href="<?php echo adminRoute('widgets/' . $view->data->page->id); ?>">
                    <i class="fas fa-th-large pr-1"></i> <?php echo e(__('toolbar.label.edit_page')); ?>
                </a>
            </li>
            <?php } ?>
            <?php if (false !== auth()->check('admin_content') && false !== auth()->check('admin_listings')) { ?>
                <?php if (isset($view->data->listing) && $view->data->listing instanceof \App\Models\Listing) { ?>
                    <li class="nav-item">
                        <a class="nav-link px-lg-2 text-light" href="<?php echo adminRoute('manage/' . $view->data->type->slug . '/summary/' . $view->data->listing->id); ?>">
                            <i class="fas fa-edit pr-1"></i> <?php echo e(__('toolbar.label.edit_listing')); ?>
                        </a>
                    </li>
                <?php } ?>
            <?php } ?>
            <li class="nav-item">
                <a class="nav-link px-lg-2 text-light" href="<?php echo adminRoute('logout'); ?>">
                    <i class="fas fa-sign-out-alt pr-1"></i> <?php echo e(__('toolbar.label.logout')); ?>
                </a>
            </li>
        </ul>
        <?php if (null !== config()->general->maintenance || null === $view->data->page->active) { ?>
            <span class="btn btn-danger btn-sm"><?php echo e(__('toolbar.label.maintenance')); ?></span>
        <?php } ?>
    </div>
</nav>
