<section class="widget categories-widget <?php echo $view->settings->colorscheme; ?> py-6">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <p class="text-caption text-black text-uppercase display-11 l-space-1"><?php echo e($view->settings->caption); ?></p>
                <h3 class="text-black display-5 l-space-0"><?php echo e($view->settings->heading); ?></h3>
            </div>
        </div>
        <div class="row mt-5">
            <?php foreach ($view->categories as $category) { ?>
            <div class="col-12 col-sm-6 col-md-6 col-lg-3 pr-1 mb-4">
                <div class="w-100 h-100">
                    <?php echo view('widgets/cards/category', [
                        'category' => $category,
                        'type' => $view->type,
                        'settings' => $view->settings,
                    ]); ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</section>
