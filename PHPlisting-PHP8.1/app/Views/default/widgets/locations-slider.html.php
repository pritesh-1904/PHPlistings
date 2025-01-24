<?php
    $id = 'slider-' . bin2hex(random_bytes(3));

    echo '<script>
    $(document).ready(function() {
        var swiper = new Swiper(".' . $id . '", {
            effect: \'slide\',
            slidesPerView: 4,
            spaceBetween: 30,
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
            pagination: {
                el: \'.swiper-pagination\',
                clickable: true,
            },
            breakpoints: {
                1200: {
                    slidesPerView: 4,
                    spaceBetween: 30,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                640: {
                    slidesPerView: 2,
                    spaceBetween: 10,
                },
                0: {
                    slidesPerView: 1,
                    spaceBetween: 10,
                }
            }
        });
    });
</script>';
?>
<section class="widget locations-slider-widget <?php echo $view->settings->colorscheme; ?> py-6">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <p class="text-caption text-black text-uppercase display-11 l-space-1"><?php echo e($view->settings->caption); ?></p>
                <h3 class="text-black display-5 l-space-0"><?php echo e($view->settings->heading); ?></h3>
            </div>
        </div>
        <div class="swiper <?php echo $id; ?> p-2 mt-5">
            <div class="swiper-wrapper pb-6">
            <?php foreach ($view->locations as $location) { ?>
                <div class="swiper-slide ht-auto">
                    <div class="w-100 h-100">
                        <?php echo view('widgets/cards/location', [
                            'location' => $location,
                            'type' => $view->type,
                            'settings' => $view->settings,
                        ]); ?>
                    </div>
                </div>
            <?php } ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>
