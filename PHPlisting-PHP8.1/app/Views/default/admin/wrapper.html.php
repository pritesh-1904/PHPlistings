<nav class="navbar p-0 fixed-top d-flex flex-row shadow-md">
    <div class="nav-brand-section bg-dark d-flex align-items-center">
        <a class="navbar-brand brand-logo pl-3" href="<?php echo adminRoute(''); ?>">
            <img src="<?php echo asset('css/default/admin/images/logo.png'); ?>" alt="logo" />
        </a>
        <a class="navbar-brand brand-logo-min" href="<?php echo adminRoute(''); ?>">
           <img src="<?php echo asset('css/default/admin/images/logo-min.png'); ?>" alt="logo-small" />
        </a>
    </div>
    <div class="nav-menu-section bg-white d-flex align-items-center justify-content-between">
        <a href="#" role="button" id="btn-hide" class="btn btn-default btn-link text-body display-8">
            <i class="fas fa-bars"></i>
        </a>
        <?php if (false !== auth()->check('admin_login')) { ?>
        <div class="pr-0 pr-md-3 display-8">
            <a title="<?php echo e(__('admin.nav.heading.website')); ?>" href="<?php echo route(''); ?>" class="text-secondary pr-3"><i class="fas fa-globe fa-fw"></i></a>
            <a title="<?php echo e(__('admin.nav.heading.myaccount')); ?>" href="<?php echo adminRoute('users/summary/' . auth()->user()->id); ?>" class="text-secondary pr-3"><i class="fas fa-user-circle fa-fw"></i></a>
            <a title="<?php echo e(__('admin.nav.heading.logout')); ?>" href="<?php echo adminRoute('logout'); ?>" class="text-secondary pr-3"><i class="fas fa-sign-out-alt fa-fw"></i></a>
        </div>
        <?php } ?>
    </div>
</nav>

<div class="wrapper">
    <aside class="sidebar bg-dark">
        <ul class="sidebar-nav">
            <li>
                <a href="<?php echo adminRoute(''); ?>">
                    <i class="fas fa-tachometer-alt fa-fw"></i>
                    <span class="menu-link"><?php echo e(__('admin.nav.heading.dashboard')); ?></span>
                </a>
            </li>
            <?php if (auth()->check('admin_login')) { ?>                    
                <?php if (false !== auth()->check('admin_settings')) { ?>
                <li>
                    <a href="<?php echo adminRoute('types/create'); ?>">
                        <i class="fas fa-plus fa-fw"></i>
                        <span class="menu-link"><?php echo e(__('admin.nav.heading.listing_type_add')); ?></span>
                    </a>
                </li>
                <?php } ?>
                <?php if (false !== auth()->check('admin_content')) { ?>
                <li class="has-dropdown">
                    <a href="#">
                        <i class="fas fa-layer-group fa-fw"></i>
                        <span class="menu-link"><?php echo e(__('admin.nav.heading.content')); ?></span>
                        <span class="right-icon">
                            <i class="fas fa-angle-right ch-right"></i>
                        </span>
                    </a>
                    <ul class="mega-menu">
                        <?php foreach (\App\Models\Type::whereNull('deleted')->orderBy('weight')->get() as $type) { ?>
                        <li class="has-dropdown">
                            <a href="#">
                                <span class="menu-link"><?php echo e(__('admin.nav.label.content', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?></span>
                                <span class="right-icon">
                                    <i class="fas fa-angle-right ch-right"></i>
                                </span>
                            </a>
                            <ul class="mega-menu">
                                <?php if (false !== auth()->check('admin_listings')) { ?>
                                    <li<?php echo (adminRouteMatch('manage/' . $type->slug) || adminRouteMatch('manage/' . $type->slug . '/summary/*') || adminRouteMatch('manage/' . $type->slug . '/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('manage/' . $type->slug); ?>"><?php echo e(__('admin.nav.label.content_view', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?></a></li>
                                    <?php if (null !== $type->approvable) { ?>
                                        <li<?php echo (adminRouteMatch('manage/' . $type->slug . '/approve')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('manage/' . $type->slug . '/approve'); ?>"><?php echo e(__('admin.nav.label.content_approve', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?> <span class="badge badge-pill badge-light"><?php echo \App\Models\Listing::where('type_id', $type->id)->whereNull('active')->count(); ?></span></a></li>
                                    <?php } ?>
                                    <?php if (null !== $type->approvable_updates) { ?>
                                        <li<?php echo (adminRouteMatch('manage/' . $type->slug . '/approve-updates') || adminRouteMatch('manage/' . $type->slug . '/approve-update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('manage/' . $type->slug . '/approve-updates'); ?>"><?php echo e(__('admin.nav.label.content_approve_updates', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?> <span class="badge badge-pill badge-light"><?php echo \App\Models\Listing::where('type_id', $type->id)->whereHas('update')->count(); ?></span></a></li>
                                    <?php } ?>
                                    <li<?php echo (adminRouteMatch('manage/' . $type->slug . '/create')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('manage/' . $type->slug . '/create'); ?>"><?php echo e(__('admin.nav.label.content_add', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?></a></li>
                                    <li<?php echo (adminRouteMatch($type->slug . '-invoices') || adminRouteMatch($type->slug . '-invoices/view/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute($type->slug . '-invoices'); ?>"><?php echo e(__('admin.nav.label.content_invoices', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?></a></li>
                                <?php } ?>

                                <?php if (false !== auth()->check('admin_reviews') && null !== $type->reviewable) { ?>
                                    <li class="has-dropdown">
                                        <a href="#">
                                            <span class="menu-link"><?php echo e(__('admin.nav.label.content_reviews', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?></span>
                                            <span class="right-icon">
                                                <i class="fas fa-angle-right ch-right"></i>
                                            </span>
                                        </a>
                                        <ul class="mega-menu">
                                            <li<?php echo (adminRouteMatch($type->slug . '-reviews') || adminRouteMatch($type->slug . '-reviews/create') || adminRouteMatch($type->slug . '-reviews/update/*') || adminRouteMatch($type->slug . '-comments') || adminRouteMatch($type->slug . '-comments/create') || adminRouteMatch($type->slug . '-comments/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute($type->slug . '-reviews'); ?>"><?php echo e(__('admin.nav.label.content_reviews_view', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?></a></li>
                                            <?php if (null !== $type->approvable_reviews) { ?>
                                                <li<?php echo (adminRouteMatch($type->slug . '-reviews/approve')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute($type->slug . '-reviews/approve'); ?>"><?php echo e(__('admin.nav.label.content_reviews_approve', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?> <span class="badge badge-pill badge-light"><?php echo \App\Models\Review::whereNull('active')->where('type_id', $type->id)->count(); ?></span></a></li>
                                            <?php } ?>
                                            <?php if (null !== $type->approvable_comments) { ?>
                                                <li<?php echo (adminRouteMatch($type->slug . '-comments/approve')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute($type->slug . '-comments/approve'); ?>"><?php echo e(__('admin.nav.label.content_reviews_comments_approve', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?> <span class="badge badge-pill badge-light"><?php echo \App\Models\Comment::whereNull('active')->where('type_id', $type->id)->count(); ?></span></a></li>
                                            <?php } ?>
                                            <?php if (false !== auth()->check('admin_fields')) { ?>
                                                <li<?php echo (adminRouteMatch('reviews-fields/' . $type->slug) || adminRouteMatch('reviews-fields/' . $type->slug . '/create') || adminRouteMatch('reviews-fields/' . $type->slug . '/update/*') || adminRouteMatch('reviews-field-constraints/' . $type->slug) || adminRouteMatch('reviews-field-constraints/' . $type->slug . '/create') || adminRouteMatch('reviews-field-constraints/' . $type->slug . '/update/*') || adminRouteMatch('reviews-field-options/' . $type->slug) || adminRouteMatch('reviews-field-options/' . $type->slug . '/create') || adminRouteMatch('reviews-field-options/' . $type->slug . '/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('reviews-fields/' . $type->slug); ?>"><?php echo e(__('admin.nav.label.content_reviews_fields', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?></a></li>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                <?php } ?>

                                <?php if (false !== auth()->check('admin_messages')) { ?>
                                <li class="has-dropdown">
                                    <a href="#">
                                        <span class="menu-link"><?php echo e(__('admin.nav.label.content_messages', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?></span>
                                        <span class="right-icon">
                                            <i class="fas fa-angle-right ch-right"></i>
                                        </span>
                                    </a>
                                    <ul class="mega-menu">
                                        <li<?php echo (adminRouteMatch($type->slug . '-messages') || adminRouteMatch($type->slug . '-messages/create') || adminRouteMatch($type->slug . '-messages/update/*') || adminRouteMatch($type->slug . '-replies') || adminRouteMatch($type->slug . '-replies/create') || adminRouteMatch($type->slug . '-replies/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute($type->slug . '-messages'); ?>"><?php echo e(__('admin.nav.label.content_messages_view', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?></a></li>
                                        <?php if (null !== $type->approvable_messages) { ?>
                                            <li<?php echo (adminRouteMatch($type->slug . '-messages/approve')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute($type->slug . '-messages/approve'); ?>"><?php echo e(__('admin.nav.label.content_messages_approve', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?> <span class="badge badge-pill badge-light"><?php echo \App\Models\Message::whereNull('active')->where('type_id', $type->id)->count(); ?></span></a></li>
                                        <?php } ?>
                                        <?php if (null !== $type->approvable_replies) { ?>
                                            <li<?php echo (adminRouteMatch($type->slug . '-replies/approve')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute($type->slug . '-replies/approve'); ?>"><?php echo e(__('admin.nav.label.content_messages_replies_approve', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?> <span class="badge badge-pill badge-light"><?php echo \App\Models\Reply::whereNull('active')->where('type_id', $type->id)->count(); ?></span></a></li>
                                        <?php } ?>
                                        <?php if (false !== auth()->check('admin_fields')) { ?>
                                            <li<?php echo (adminRouteMatch('messages-fields/' . $type->slug) || adminRouteMatch('messages-fields/' . $type->slug . '/create') || adminRouteMatch('messages-fields/' . $type->slug . '/update/*') || adminRouteMatch('messages-field-constraints/' . $type->slug) || adminRouteMatch('messages-field-constraints/' . $type->slug . '/create') || adminRouteMatch('messages-field-constraints/' . $type->slug . '/update/*') || adminRouteMatch('messages-field-options/' . $type->slug) || adminRouteMatch('messages-field-options/' . $type->slug . '/create') || adminRouteMatch('messages-field-options/' . $type->slug . '/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('messages-fields/' . $type->slug); ?>"><?php echo e(__('admin.nav.label.content_messages_fields', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?></a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                                <?php } ?>

                                <?php if (false !== auth()->check('admin_claims')) { ?>
                                    <li<?php echo (adminRouteMatch($type->slug . '-claims') || adminRouteMatch($type->slug . '-claims/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute($type->slug . '-claims'); ?>"><?php echo e(__('admin.nav.label.content_claims', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?> <span class="badge badge-pill badge-light"><?php echo \App\Models\Claim::where('status', 'pending')->where('type_id', $type->id)->count(); ?></span></a></li>
                                <?php } ?>

                                <?php if (false !== auth()->check('admin_categories')) { ?>
                                    <li<?php echo (adminRouteMatch('categories/' . $type->slug) || adminRouteMatch('categories/' . $type->slug . '/*') || adminRouteMatch('categories/' . $type->slug . '/*/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('categories/' . $type->slug); ?>"><?php echo e(__('admin.nav.label.content_categories', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?></a></li>
                                <?php } ?>

                                <?php if (false !== auth()->check('admin_products')) { ?>
                                    <li<?php echo (adminRouteMatch('products/' . $type->slug) || adminRouteMatch('products/' . $type->slug . '/create') || adminRouteMatch('products/' . $type->slug . '/update/*') || adminRouteMatch('pricings/' . $type->slug) || adminRouteMatch('pricings/' . $type->slug . '/create') || adminRouteMatch('pricings/' . $type->slug . '/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('products/' . $type->slug); ?>"><?php echo e(__('admin.nav.label.content_products', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?></a></li>
                                <?php } ?>

                                <?php if (false !== auth()->check(['admin_listings', 'admin_fields'])) { ?>
                                    <li<?php echo (adminRouteMatch('listings-fields/' . $type->slug) || adminRouteMatch('listings-fields/' . $type->slug . '/create') || adminRouteMatch('listings-fields/' . $type->slug . '/update/*') || adminRouteMatch('listings-field-constraints/' . $type->slug) || adminRouteMatch('listings-field-constraints/' . $type->slug . '/create') || adminRouteMatch('listings-field-constraints/' . $type->slug . '/update/*') || adminRouteMatch('listings-field-options/' . $type->slug) || adminRouteMatch('listings-field-options/' . $type->slug . '/create') || adminRouteMatch('listings-field-options/' . $type->slug . '/create-multiple') || adminRouteMatch('listings-field-options/' . $type->slug . '/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('listings-fields/' . $type->slug); ?>"><?php echo e(__('admin.nav.label.content_fields', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?></a></li>
                                <?php } ?>

                                <li<?php echo (adminRouteMatch($type->slug . '-badges') || adminRouteMatch($type->slug . '-badges/create') || adminRouteMatch($type->slug . '-badges/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute($type->slug . '-badges'); ?>"><?php echo e(__('admin.nav.label.badges', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?></a></li>

                                <?php if (false !== auth()->check('admin_import')) { ?>
                                    <li<?php echo (adminRouteMatch($type->slug . '-import') || adminRouteMatch($type->slug . '-import/create')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute($type->slug . '-import'); ?>"><?php echo e(__('admin.nav.label.content_import', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?></a></li>
                                <?php } ?>

                                <?php if (false !== auth()->check('admin_export')) { ?>
                                    <li<?php echo (adminRouteMatch($type->slug . '-export') || adminRouteMatch($type->slug . '-export/create')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute($type->slug . '-export'); ?>"><?php echo e(__('admin.nav.label.content_export', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?></a></li>
                                <?php } ?>

                                <?php if (false !== auth()->check('admin_listings')) { ?>
                                    <?php if (null !== config()->other->deadlinkchecker) { ?>
                                        <li<?php echo (adminRouteMatch($type->slug . '-broken-links') || adminRouteMatch($type->slug . '-broken-links/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute($type->slug . '-broken-links'); ?>"><?php echo e(__('admin.nav.label.content_deadlinkchecker', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?> <span class="badge badge-pill badge-light"><?php echo \App\Models\Listing::where('type_id', $type->id)->whereNotNull('deadlinkchecker_retry')->count(); ?></span></a></li>
                                    <?php } ?>
                                    <?php if (null !== config()->other->backlinkchecker) { ?>
                                        <li<?php echo (adminRouteMatch($type->slug . '-invalid-backlinks') || adminRouteMatch($type->slug . '-invalid-backlinks/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute($type->slug . '-invalid-backlinks'); ?>"><?php echo e(__('admin.nav.label.content_backlinkchecker', ['singular' => $type->name_singular, 'plural' => $type->name_plural])); ?> <span class="badge badge-pill badge-light"><?php echo \App\Models\Listing::where('type_id', $type->id)->whereNotNull('backlinkchecker_retry')->count(); ?></span></a></li>
                                    <?php } ?>
                                <?php } ?>                            
                            </ul>
                        </li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if (false !== auth()->check('admin_users')) { ?>
                <li class="has-dropdown">
                    <a href="#">
                        <i class="fas fa-user fa-fw"></i>
                        <span class="menu-link"><?php echo e(__('admin.nav.heading.users')); ?></span>
                        <span class="right-icon">
                            <i class="fas fa-angle-right ch-right"></i>
                        </span>
                    </a>
                    <ul class="mega-menu">
                        <li<?php echo (adminRouteMatch('users') || adminRouteMatch('users/update/*') || adminRouteMatch('users/summary/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('users'); ?>"><?php echo e(__('admin.nav.label.users')); ?></a></li>
                        <li<?php echo (adminRouteMatch('users/create')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('users/create'); ?>"><?php echo e(__('admin.nav.label.users_add')); ?></a></li>
                        <?php if (null !== config()->account->approval) { ?>
                            <li<?php echo (adminRouteMatch('users/approve')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('users/approve'); ?>"><?php echo e(__('admin.nav.label.users_approve')); ?> <span class="badge badge-pill badge-light"><?php echo \App\Models\User::whereNull('active')->count(); ?></span></a></li>
                        <?php } ?>
                        <li<?php echo (adminRouteMatch('user-groups') || adminRouteMatch('user-groups/*') || adminRouteMatch('user-groups/*/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('user-groups'); ?>"><?php echo e(__('admin.nav.label.user_groups')); ?></a></li>
                        <?php if (false !== auth()->check('admin_fields')) { ?>
                            <li<?php echo (adminRouteMatch('fields/users') || adminRouteMatch('fields/users/create') || adminRouteMatch('fields/users/update/*') || adminRouteMatch('field-constraints/users') || adminRouteMatch('field-constraints/users/create') || adminRouteMatch('field-constraints/users/update/*') || adminRouteMatch('field-options/users') || adminRouteMatch('field-options/users/create') || adminRouteMatch('field-options/users/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('fields/users'); ?>"><?php echo e(__('admin.nav.label.user_fields')); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if (false !== auth()->check('admin_files')) { ?>
                <li<?php echo (adminRouteMatch('files')) ? ' class="active"' : ''; ?>>
                    <a href="<?php echo adminRoute('files'); ?>">
                        <i class="fas fa-photo-video fa-fw"></i>
                        <span class="menu-link"><?php echo e(__('admin.nav.heading.media')); ?></span>
                    </a>
                </li>
                <?php } ?>

                <?php if (false !== auth()->check('admin_locations')) { ?>
                <li<?php echo (adminRouteMatch('locations') || adminRouteMatch('locations/*') || adminRouteMatch('locations/*/*') ) ? ' class="active"' : ''; ?>>
                    <a href="<?php echo adminRoute('locations'); ?>">
                        <i class="fas fa-map-marker-alt fa-fw"></i>
                        <span class="menu-link"><?php echo e(__('admin.nav.heading.locations')); ?></span>
                    </a>
                </li>                            
                <?php } ?>

                <?php if (false !== auth()->check('admin_emails')) { ?>
                <li class="has-dropdown">
                    <a href="#">
                        <i class="fas fa-envelope fa-fw"></i>
                        <span class="menu-link"><?php echo e(__('admin.nav.heading.email')); ?></span>
                        <span class="right-icon">
                            <i class="fas fa-angle-right ch-right"></i>
                        </span>
                    </a>
                    <ul class="mega-menu">
                        <li<?php echo (adminRouteMatch('email-queue') || adminRouteMatch('email-queue/view/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('email-queue'); ?>"><?php echo e(__('admin.nav.label.email_queue')); ?></a></li>
                        <li<?php echo (adminRouteMatch('email-templates') || adminRouteMatch('email-templates/create') || adminRouteMatch('email-templates/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('email-templates'); ?>"><?php echo e(__('admin.nav.label.email_templates')); ?></a></li>
                    </ul>
                </li>
                <?php } ?>

                <?php if (false !== auth()->check('admin_appearance')) { ?>
                <li class="has-dropdown">
                    <a href="#">
                        <i class="fas fa-paint-brush fa-fw"></i>
                        <span class="menu-link"><?php echo e(__('admin.nav.heading.appearance')); ?></span>
                        <span class="right-icon">
                            <i class="fas fa-angle-right ch-right"></i>
                        </span>
                    </a>
                    <ul class="mega-menu">
                        <li<?php echo (adminRouteMatch('themes') || adminRouteMatch('themes/*') || adminRouteMatch('themes/*/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('themes'); ?>"><?php echo e(__('admin.nav.label.themes')); ?></a></li>
                        <li<?php echo (adminRouteMatch('pages') || adminRouteMatch('pages/*') || adminRouteMatch('pages/*/*') || adminRouteMatch('widgets/*') || adminRouteMatch('widgets/*/*') || adminRouteMatch('widgets/*/*/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('pages'); ?>"><?php echo e(__('admin.nav.label.pages')); ?></a></li>
                        <li<?php echo (adminRouteMatch('menu-groups') || adminRouteMatch('menu-groups/*') || adminRouteMatch('menu-groups/*/*') || adminRouteMatch('menu/*') || adminRouteMatch('menu/*/*') || adminRouteMatch('menu/*/*/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('menu-groups'); ?>"><?php echo e(__('admin.nav.label.menus')); ?></a></li>
                        <?php if (false !== auth()->check('admin_fields')) { ?>
                            <li<?php echo (adminRouteMatch('widget-field-groups') || adminRouteMatch('widget-field-groups/*') || adminRouteMatch('widget-field-groups/*/*') || adminRouteMatch('widget-fields/*') || adminRouteMatch('widget-fields/*/*') || adminRouteMatch('widget-fields/*/*/*') || adminRouteMatch('widget-field-constraints/*') || adminRouteMatch('widget-field-constraints/*/*') || adminRouteMatch('widget-field-constraints/*/*/*') || adminRouteMatch('widget-field-options/*') || adminRouteMatch('widget-field-options/*/*') || adminRouteMatch('widget-field-options/*/*/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('widget-field-groups'); ?>"><?php echo e(__('admin.nav.label.forms')); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if (false !== auth()->check('admin_settings')) { ?>
                <li class="has-dropdown">
                    <a href="#">
                        <i class="fas fa-cog fa-fw"></i>
                        <span class="menu-link"><?php echo e(__('admin.nav.heading.settings')); ?></span>
                        <span class="right-icon">
                            <i class="fas fa-angle-right ch-right"></i>
                        </span>
                    </a>
                    <ul class="mega-menu">
                        <li<?php echo (adminRouteMatch('settings/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('settings'); ?>"><?php echo e(__('admin.nav.label.settings')); ?></a></li>
                        <li<?php echo (adminRouteMatch('types') || adminRouteMatch('types/create') || adminRouteMatch('types/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('types'); ?>"><?php echo e(__('admin.nav.label.types')); ?></a></li>
                        <li<?php echo (adminRouteMatch('payment-gateways') || adminRouteMatch('payment-gateways/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('payment-gateways'); ?>"><?php echo e(__('admin.nav.label.gateways')); ?></a></li>
                        <li<?php echo (adminRouteMatch('discounts') || adminRouteMatch('discounts/create') || adminRouteMatch('discounts/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('discounts'); ?>"><?php echo e(__('admin.nav.label.discounts')); ?></a></li>
                        <li<?php echo (adminRouteMatch('tax-rates') || adminRouteMatch('tax-rates/create') || adminRouteMatch('tax-rates/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('tax-rates'); ?>"><?php echo e(__('admin.nav.label.taxes')); ?></a></li>
                        <li<?php echo (adminRouteMatch('languages') || adminRouteMatch('languages/create') || adminRouteMatch('languages/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('languages'); ?>"><?php echo e(__('admin.nav.label.languages')); ?></a></li>
                        <li<?php echo (adminRouteMatch('upload-types') || adminRouteMatch('upload-types/create') || adminRouteMatch('upload-types/update/*') || adminRouteMatch('file-types') || adminRouteMatch('file-types/create') || adminRouteMatch('file-types/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('upload-types'); ?>"><?php echo e(__('admin.nav.label.upload_types')); ?></a></li>
                        <li<?php echo (adminRouteMatch('rating-categories') || adminRouteMatch('rating-categories/create') || adminRouteMatch('rating-categories/update/*')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('rating-categories'); ?>"><?php echo e(__('admin.nav.label.rating_categories')); ?></a></li>
                        <li<?php echo (adminRouteMatch('tasks')) ? ' class="active"' : ''; ?>><a href="<?php echo adminRoute('tasks'); ?>"><?php echo e(__('admin.nav.label.tasks')); ?></a></li>
                    </ul>
                </li>
                <?php } ?>

                <li>
                    <a href="<?php echo adminRoute('logout'); ?>">
                        <i class="fas fa-sign-out-alt fa-fw"></i>
                        <span class="menu-link"><?php echo e(__('admin.nav.heading.logout')); ?></span>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </aside>    

    <div class="mid-container bg-light">
        <div class="container-wrapper p-4">
            <?php echo $view->content; ?>
        </div>
        <footer class="footer p-4 display-11">
            <hr class="my-4">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-center text-sm-left d-block d-sm-inline-block">
                    Powered by <a href="https://www.phplistings.com">phpListings</a> (v1.0.8)
                </span>
                <span class="float-none float-sm-right d-block text-center mt-lg-0 mt-sm-4">
                    &copy; <?php echo date('Y'); ?> phpListings.com
                </span>
            </div>
        </footer>
    </div>
    <img src="<?php echo route('cron'); ?>" alt=""/>
</div>
