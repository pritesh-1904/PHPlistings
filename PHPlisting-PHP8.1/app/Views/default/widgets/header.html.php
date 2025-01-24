<header class="widget header-widget navbar-top">
    <nav class="navbar navbar-expand-lg <?php echo $view->settings->colorscheme; ?> shadow-md">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo route(''); ?>">
            <?php
                if ('' != $view->settings->get('logo', '') && null !== $file = \App\Models\File::where('document_id', $view->settings->logo)->first()) {
                    if (false !== $file->isImage()) {
                        $attributes = attr([
                            'src' => $file->small()->getUrl(),
                            'width' => $file->small()->getWidth(),
                            'height' => $file->small()->getHeight(),
                            'alt' => config()->general->site_name,
                        ]);
                        
                        echo '<img ' . $attributes . ' />';
                    }
                } else {
                    echo '<img src="' . asset('/css/default/images/logo.png') . '" alt="' . config()->general->site_name . '" />';
                }
            ?>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav <?php echo null !== $view->settings->button ? 'mx-auto' : 'ml-auto'; ?>">
                <?php 
                    $items = \App\Models\WidgetMenuGroup::find($view->settings->menu_group)->getTree();

                    foreach ($items as $item) {
                        if (count($item->children) > 0) {
                            echo '<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" id="nav-link-dropdown-' . e($item->id) . '" data-toggle="dropdown" aria-expanded="false" aria-haspopup="true" href="#" role="button">' . e($item->name) . '</a>';
                            echo '<div class="dropdown-menu shadow-md" aria-labelledby="nav-link-dropdown-' . e($item->id) . '">';
                            foreach ($item->children as $child) {
                                echo '<a class="dropdown-item' . (null !== $child->get('highlighted') ? ' highlighted-item' : '') . '" href="' . e($child->getLink()) . '" target="' . e($child->target) . '"' . (null !== $child->nofollow ? ' rel="nofollow"' : '') . '>' . e($child->name) . '</a>';
                            }
                            echo '</div>';
                            echo '</li>';
                        } else {
                            echo '<li class="nav-item' . (null !== $item->get('highlighted') ? ' highlighted-item' : '') . '"><a class="nav-link" href="' . e($item->getLink()) . '" target="' . e($item->target) . '"' . (null !== $item->nofollow ? ' rel="nofollow"' : '') . '>' . e($item->name) . '</a></li>';
                        }
                    }

                    $languages = locale()->getSupportedWithOptions();

                    if ($languages->count() > 1) {
                        foreach ($languages as $language) {
                            if ($language->locale != locale()->getLocale()) {
                                $query = [];

                                if ('1' == request()->get->get('page')) {
                                    $query = collect(request()->get->all())->forget('page')->all();
                                } else {
                                    $query = request()->get->all();
                                }
                                
                                echo '<li class="nav-item"><a class="nav-link" href="' . route(getRoute(), $query, $language->locale) . '">' . e($language->native) . '</a></li>';
                            }
                        }
                    }
                ?>
                </ul>
                <?php if (null !== $view->settings->button) { ?>
                <div class="d-flex align-items-center justify-content-between justify-content-lg-end">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <?php if (false !== auth()->check('user_login')) { ?>
                                <div class="btn-group pr-2">
                                    <a title="<?php echo e(__('account.widget.header.label.dashboard')); ?>" class="btn btn-round btn-primary" href="<?php echo route('account'); ?>"><i class="fas fa-user-circle"></i> <?php echo e(auth()->user()->first_name); ?></a>
                                    <a title="<?php echo e(__('account.widget.header.label.logout')); ?>" class="btn btn-round btn-danger px-2" href="<?php echo route('account/logout'); ?>"><i class="fas fa-sign-out-alt"></i></a>
                                </div>
                            <?php } else { ?>
                                <a class="btn btn-round btn-primary" href="<?php echo route('account/login'); ?>"><?php echo e(__('account.widget.header.label.signin')); ?></a>
                            <?php } ?>
                        </li>
                    </ul>
                </div>
                <?php } ?>
            </div>
        </div>
    </nav>
</header>