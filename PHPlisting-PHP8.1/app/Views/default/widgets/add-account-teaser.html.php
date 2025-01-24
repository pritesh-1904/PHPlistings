<section class="widget add-account-teaser-widget <?php echo $view->settings->colorscheme; ?> py-6">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="text-center">
                    <p class="text-caption text-uppercase text-black l-space-1 display-11">
                        <?php echo e($view->settings->caption); ?>
                    </p>
                    <h3 class="display-5 text-black l-space-0">
                        <?php echo e($view->settings->heading); ?>
                    </h3>
                    <p class="my-5">
                        <?php echo d($view->settings->description); ?>
                    </p>
                    <p class="m-0">
                        <a class="btn btn-lg btn-round btn-primary" href="<?php echo route('account/create'); ?>" role="button"><?php echo e($view->settings->button); ?></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>          
