<section class="widget three-column-teaser-widget trio-section-text <?php echo $view->settings->colorscheme; ?> py-6">
    <div class="container">
        <div class="text-center">
            <p class="text-caption text-uppercase text-black l-space-1 display-11"><?php echo e($view->settings->caption); ?></p>
            <h3 class="text-black l-space-0 display-5"><?php echo e($view->settings->heading); ?></h3>
        </div>
        <div class="row">
            <div class="col-12 col-lg-4 text-center">
                <div class="box-info-inner">
                    <div class="icon-top my-5">
                        <i class="<?php echo $view->settings->first_icon; ?> display-0 text-primary"></i>
                    </div>
                    <h4 class="text-bold display-8 mb-3"><?php echo d($view->settings->first_heading); ?></h4>
                    <p>
                        <?php echo d($view->settings->first_paragraph); ?>
                    </p>
                </div>
            </div>
            <div class="col-12 col-lg-4 text-center">
                <div class="box-info-inner">
                    <div class="icon-top my-5">
                        <i class="<?php echo $view->settings->second_icon; ?> display-0 text-primary"></i>
                    </div>
                    <h4 class="text-bold display-8 mb-3"><?php echo d($view->settings->second_heading); ?></h4>
                    <p>
                        <?php echo d($view->settings->second_paragraph); ?>
                    </p>
                </div>
            </div>
            <div class="col-12 col-lg-4 text-center">
                <div class="box-info-inner">
                    <div class="icon-top my-5">
                        <i class="<?php echo $view->settings->third_icon; ?> display-0 text-primary"></i>
                    </div>
                    <h4 class="text-bold display-8 mb-3"><?php echo d($view->settings->third_heading); ?></h4>
                    <p>
                        <?php echo d($view->settings->third_paragraph); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
