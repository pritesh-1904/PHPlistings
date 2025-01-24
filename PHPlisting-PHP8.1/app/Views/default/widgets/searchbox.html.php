<?php
    $id = 'slider-' . bin2hex(random_bytes(3));

    layout()->addCss('<link href="' . asset('js/swiper/css/swiper.min.css?v=844') . '" rel="stylesheet">');
    layout()->addFooterJs('<script src="' . asset('js/swiper/js/swiper.min.js?v=844') . '"></script>');
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        var swiper = new Swiper(".' . $id . '", {
            effect: "fade",
            speed: ' . e($view->settings->slider_speed) . ',
            autoplay: {
                delay: ' . e($view->settings->slider_autoplay_delay) . ',
            },
            preloadImages: false,
        });
    });
    </script>');

    $keywordWidth = 10;
    $blockWidth = 0;

    $blocks = 1 + $view->type->localizable;

    if ('Event' == $view->type->type) {
        $blocks++;
    }

    if (1 == $blocks) {
        $keywordWidth = 5;
        $blockWidth = 5;
    } else if (2 == $blocks) {
        $keywordWidth = 4;
        $blockWidth = 3;
    } else if (3 == $blocks) {
        $keywordWidth = 4;
        $blockWidth = 2;
    }
?>
<section class="widget searchbox-widget top-hero d-flex position-relative align-items-center">
    <div class="swiper header-hero position-absolute w-100 h-100 <?php echo $id; ?>">
        <div class="swiper-wrapper dark-fill">
        <?php 
            if ('' != $view->settings->slider) {
                $images = \App\Models\File::where('document_id', $view->settings->slider)->get();

                if ($images->count() > 0) {
                    foreach ($images as $image) {
                        echo '<div class="swiper-slide h-100" style="background-image:url(' . $image->large()->getUrl() . ')"></div>';
                    }
                }
            }
        ?>
        </div>
    </div>
    <div class="container py-5 py-lg-10 box-overlay">
        <div class="row">
            <div class="col-lg-12 mx-auto">
                <div class="text-center text-white">
                    <h1 class="text-bold display-1 mb-2 text-shadow"><?php echo d($view->settings->heading); ?></h1>
                    <h2 class="text-thin display-9 l-space-2 text-shadow"><?php echo d($view->settings->description); ?></h2>
                </div>
                <div class="search-form-top bg-white mt-4 p-3 p-lg-1 pl-lg-4">
                    <form method="get" action="<?php echo route($view->type->slug . '/search'); ?>">
                        <div class="form-row">
                            <div class="col-lg-<?php echo $keywordWidth; ?> field-line">
                                <?php echo $view->form->getFields()->keyword->render(); ?>
                            </div>
                            <?php if ('Event' == $view->type->type) { ?>
                            <div class="col-lg-<?php echo $blockWidth; ?> field-line">
                                <?php echo $view->form->getFields()->dates->render(); ?>
                            </div>
                            <?php } ?>
                            <?php if (null !== $view->type->localizable) { ?>
                            <div class="col-lg-<?php echo $blockWidth; ?> field-line">
                                <?php echo $view->form->getFields()->location_id->render(); ?>
                            </div>
                            <?php } ?>
                            <div class="col-lg-<?php echo $blockWidth; ?>">
                                <?php echo $view->form->getFields()->category_id->render(); ?>
                            </div>
                            <div class="col-lg-2">
                                <div class="float-lg-right mt-3 mt-lg-0">
                                    <button type="submit" name="submit" class="btn btn-primary btn-block"><?php echo e(__('listing.search.form.label.submit')); ?></button>
                                </div>
                            </div>
                        </div> 
                    </form>
                </div>
                <?php if (null !== $view->settings->types && $view->types->count() > 1) { ?>
                <div class="row text-center justify-content-center no-gutters mt-5 pb-100 py-lg-0">
                    <?php foreach ($view->types as $type) { ?>
                        <?php if ($view->type->id != $type->id) { ?>
                            <div class="col-sm-6 col-md-4 col-lg-2 px-2 mb-4">
                                <a class="card bg-opacity h-100 shadow-md" href="<?php echo route($type->slug); ?>" title="<?php echo e($type->name_plural); ?>">
                                    <div class="card-body py-3">
                                        <i class="<?php echo e($type->icon); ?> text-white text-shadow display-6 mb-3"></i>
                                        <p class="card-title text-white text-shadow display-10 my-0"><?php echo e($type->name_plural); ?></p>
                                    </div>
                                </a>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>              
</section>
