<?php
    $id = 'slider-' . bin2hex(random_bytes(3));

    layout()->addCss('<link href="' . asset('js/swiper/css/swiper.min.css?v=844') . '" rel="stylesheet">');
    layout()->addFooterJs('<script src="' . asset('js/swiper/js/swiper.min.js?v=844') . '"></script>');
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        var swiper = new Swiper(".' . $id . '", {
            effect: "slide",
            speed: ' . e($view->settings->slider_speed) . ',
            ' .
            (
                (null !== $view->settings->slider_autoplay)
                ? 
                    'autoplay: {
                        delay: ' . e($view->settings->slider_autoplay_delay) . ',
                    },'
                :
                    ''
            ) . '
            slidesPerView: ' . e($view->settings->slider_slides_per_view) . ',
            spaceBetween: ' . e($view->settings->slider_space_between) . ',
            ' .
            (
                (null !== $view->settings->slider_navigation)
                ? 
                    'navigation: {
                        nextEl: \'.swiper-button-next\',
                        prevEl: \'.swiper-button-prev\',
                    },'
                :
                    ''
            ) . '
        });
    });
    </script>');
?>
<section class="widget slider-widget">
    <div class="swiper w-100 h-100 <?php echo $id; ?>">
        <div class="swiper-wrapper">
        <?php 
            if ('' != $view->settings->slider) {
                $images = \App\Models\File::where('document_id', $view->settings->slider)->get();

                if ($images->count() > 0) {
                    foreach ($images as $image) {
                        if (false !== $image->isImage()) {
                            $attributes = attr([
                                'src' => $image->large()->getUrl(),
                                'width' => $image->large()->getWidth(),
                                'height' => $image->large()->getHeight(),
                                'class' => 'img-fluid w-100',
                                'alt' => e($image->title),
                            ]);

                            echo '
                                <div class="swiper-slide">
                                    <img ' . $attributes . ' />
                            ';

                            if ('' != $image->get('title', '')) {
                                echo '
                                    <div class="caption p-3">
                                        <span class="display-9 text-white">
                                            ' . e($image->title) . '
                                        </span>
                                ';

                                if ('' != $image->get('description', '')) {
                                    echo '
                                        <br />
                                        <span class="display-12 text-white">
                                            ' . e($image->description) . '
                                        </span>
                                    ';
                                }

                                echo '
                                    </div>
                                ';
                            }

                            echo '
                                </div>
                            ';
                        }
                    }
                }
            }
        ?>
        </div>
        <?php if (null !== $view->settings->slider_navigation) { ?>
        <div class="swiper-button-next swiper-button-white"></div>
        <div class="swiper-button-prev swiper-button-white"></div>
        <?php } ?>
    </div>
</section>
