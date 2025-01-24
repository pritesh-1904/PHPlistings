<?php
    $ads = [];

    foreach (['block_1', 'block_2', 'block_3', 'block_4'] as $block) {
        if (null !== $view->settings->get($block) && '' != $view->settings->get($block)) {
            $ads[] = $view->settings->get($block);
        }
    }
?>
<section class="widget banner-widget <?php echo $view->settings->colorscheme; ?> py-3">
    <div class="container">
        <div class="row">
            <?php foreach ($ads as $ad) { ?>
                <div class="col-12 col-md-<?php echo round(12 / count($ads)); ?> text-center p-3">
                    <?php echo d($ad); ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
