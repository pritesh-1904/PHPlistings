<section class="widget two-column-teaser-widget trio-section-text <?php echo $view->settings->colorscheme; ?> py-6">
    <div class="container">
        <div class="row">
            <?php if ('' !== $view->settings->image && null !== $image = \App\Models\File::where('document_id', $view->settings->image)->first()) { ?>
                <?php if (false !== $image->isImage()) { ?>
                    <div class="col-12 col-lg-6 d-none d-lg-block widget-two-column-teaser-image order-<?php echo e($view->settings->image_order); ?>" style="background-image:url('<?php echo $image->large()->getUrl(); ?>');"></div>
                <?php } ?>
            <?php } ?>
            <div class="col-12<?php echo (null !== $image && false !== $image->isImage()) ? ' col-lg-6' : ''; ?> order-<?php echo ('1' == $view->settings->image_order) ? 2 : 1; ?> px-2 px-lg-5 py-4">
                <p class="text-caption text-uppercase text-black l-space-1 display-11"><?php echo e($view->settings->caption); ?></p>
                <h3 class="text-black l-space-0 display-5 mb-4"><?php echo d($view->settings->heading); ?></h3>
                <p><?php echo d($view->settings->paragraph); ?></p>
                <?php if ('' != $view->settings->button) { ?>
                    <p class="mt-5"><?php echo d($view->settings->button); ?></p>
                <?php } ?>
            </div>
        </div>
    </div>
</section>
