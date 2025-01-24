<?php
    echo '<script>
    $(document).ready(function() {
        var swiper = new Swiper(\'.listing-gallery-slider\', {
            effect: \'slide\',
            spaceBetween: 0,
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
            navigation: {
                nextEl: \'.swiper-button-next\',
                prevEl: \'.swiper-button-prev\',
            },  
            breakpoints: {
                768: {
                    slidesPerView: 3,
                },
                0: {
                    slidesPerView: 1,
                }
            }
        });
    });
    </script>';
?>
<?php if ($view->images->count() > 0 ) { ?>
<section class="widget listing-gallery-slider-widget">
    <div class="swiper listing-gallery-slider">
        <div class="swiper-wrapper">
            <?php foreach ($view->images as $image) { ?>
                <?php if (false !== $image->isImage()) { ?>
                    <?php
                        $attributes = attr([
                            'src' => $image->medium()->getUrl(),
                            'width' => $image->medium()->getWidth(),
                            'height' => $image->medium()->getHeight(),
                            'class' => 'img-fluid w-100',
                            'alt' => e($image->title),
                        ]);
                    ?>
                    <div class="swiper-slide position-relative">
                        <img <?php echo $attributes; ?> />
                        <?php
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
                        ?>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="swiper-button-next swiper-button-white"></div>
        <div class="swiper-button-prev swiper-button-white"></div>
    </div>
</section>
<?php } ?>
