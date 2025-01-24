<section class="widget reviews-widget <?php echo $view->settings->colorscheme; ?> py-6">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <p class="text-caption text-black text-uppercase display-11 l-space-1"><?php echo e($view->settings->caption); ?></p>
                <h3 class="text-black display-5 l-space-0"><?php echo e($view->settings->heading); ?></h3>
            </div>
        </div>
        <div class="row mt-5">
            <?php foreach ($view->reviews as $review) { ?>
                <div class="col-12 col-md-6 col-lg-4 mb-4">
                     <div class="w-100 h-100">
                        <?php echo view('widgets/cards/review', [
                            'review' => $review,
                            'type' => $view->type,
                            'settings' => $view->settings,
                            'data' => $view->data,
                        ]); ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
