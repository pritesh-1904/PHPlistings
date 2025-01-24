<div class="col-12 col-lg-3 mb-md-4">
    <nav class="navbar navbar-light navbar-expand-lg flex-row flex-sm-column align-items-start side-menu">
        <button class="navbar-toggler border-0 mb-lg-0 mb-3 text-dark" type="button" data-toggle="collapse" data-target="#account_navbar"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-list display-7"></i>
        </button>
        <div class="navbar-collapse collapse w-100" id="account_navbar">
            <ul class="nav flex-column side-menu">
                <li class="nav-item">
                    <a class="nav-link my-lg-1" href="<?php echo route('account'); ?>"><?php echo __('account.menu.dashboard'); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link my-lg-1" href="<?php echo route('account/bookmarks'); ?>"><?php echo __('account.menu.bookmarks', ['count' => '<span class="badge badge-pill badge-light ml-1">' . auth()->user()->bookmarks()->count() . '</span>']); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link my-lg-1" href="<?php echo route('account/reviews'); ?>"><?php echo __('account.menu.reviews'); ?></a>
                </li>
                <?php
                    $query = (new \App\Models\Type)->whereNull('deleted')->orderBy('weight');

                    if (false === auth()->check('admin_login')) {
                        $query->whereNotNull('active');
                    }
            
                    $types = $query->get();
                ?>
                <?php foreach ($types as $type) { ?>
                    <li class="nav-item">
                        <a class="nav-link my-lg-1" href="<?php echo route('account/manage/' . $type->slug); ?>"><?php echo __('account.menu.type', ['singular' => $type->name_singular, 'plural' => $type->name_plural, 'count' => '<span class="badge badge-pill badge-light ml-2">' . $type->listings()->where('user_id', auth()->user()->id)->count() . '</span>']); ?></a>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link my-lg-1" href="<?php echo route('account/claims'); ?>"><?php echo __('account.menu.claims'); ?></a>
                </li>
                <li class="nav-item">
                    <?php $messages = auth()->user()->inbox()->whereNotNull('active')->where(function ($query) {
                            $query
                                ->whereNull('read_datetime')
                                ->orWhereHas('replies', function ($query) { 
                                    return $query
                                        ->where('user_id', '!=', auth()->user()->id)
                                        ->whereNull('read_datetime');
                                });
                        })
                        ->count();
                    ?>
                    <a class="nav-link my-lg-1" href="<?php echo route('account/messages'); ?>"><?php echo __('account.menu.messages', ['count' => ($messages > 0 ? '<span class="badge badge-pill badge-danger ml-1">' . $messages . '</span>' : '')]); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link my-lg-1" href="<?php echo route('account/invoices'); ?>"><?php echo __('account.menu.invoices'); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link my-lg-1" href="<?php echo route('account/profile'); ?>"><?php echo __('account.menu.profile'); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link my-lg-1" href="<?php echo route('account/logout'); ?>"><?php echo __('account.menu.logout'); ?></a>
                </li>
            </ul>
        </div>
    </nav>
</div>
