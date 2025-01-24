<?php
    $field = $view->data->listing->data->where('field_name', 'opening_hours_id')->first();
    if (null !== $field && null !== $field->active && '' != $field->get('value', '')) {
        $hours = \App\Models\Hour::where('hash', $field->value)->orderBy('dow')->orderBy('start_time')->get(['dow', 'start_time', 'end_time']);
    }
?>
<section class="widget listing-header-widget <?php echo $view->settings->colorscheme; ?> py-6">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-lg-8">
                <p class="text-success link-success text-uppercase text-black display-11 l-space-1 mb-1">
                    <a href="<?php echo route($view->data->listing->type->slug); ?>"><?php echo e($view->data->listing->type->name_plural); ?></a> &raquo; <?php echo $view->data->listing->getOutputableValue('_category_links'); ?> &raquo; <a href="<?php echo route($view->data->listing->type->slug . '/' . $view->data->listing->slug); ?>"><?php echo e($view->data->listing->title); ?></a>
                </p>
                <h1 class="display-3 text-black mb-3">
                    <?php echo $view->data->listing->getOutputableValueWithSchema('_title'); ?>
                </h1>
                <div class="display-8 mb-3">
                    <?php if ($view->data->listing->isFeatured()) { ?>
                        <span class="badge badge-pill text-sm badge-danger"><?php echo e(__('listing.badge.featured')); ?></span>
                    <?php } ?>
                    <?php if ($view->data->listing->isNew()) { ?>
                        <span class="badge badge-pill text-sm badge-info"><?php echo e(__('listing.badge.new')); ?></span>
                    <?php } ?>
                    <?php if ($view->data->listing->isUpdated()) { ?>
                        <span class="badge badge-pill text-sm badge-secondary"><?php echo e(__('listing.badge.updated')); ?></span>
                    <?php } ?>
                    <?php if ($view->data->listing->isHot()) { ?>
                        <span class="badge badge-pill text-sm badge-warning"><?php echo e(__('listing.badge.hot')); ?></span>
                    <?php } ?>
                    <?php if (isset($hours) && $view->data->listing->isOpen($hours)) { ?>
                        <span class="badge badge-pill text-sm badge-success"><?php echo e(__('listing.badge.open_now')); ?></span>
                    <?php } ?>
                </div>
                <?php if (null !== $view->data->listing->type->reviewable && null !== $view->data->listing->get('_reviews')) {
                    if ($view->data->listing->review_count > 0) { ?>
                        <p class="m-0 mb-3 text-warning text-nowrap display-11" title="<?php echo e(__('listing.header.label.rating', ['stars' => e($view->data->listing->rating), 'reviews' => e($view->data->listing->review_count)], (int) $view->data->listing->review_count)); ?>">
                                <?php echo $view->data->listing->getOutputableValue('_rating'); ?>
                                <span class="ml-2 text-dark">
                                    <?php echo __('listing.header.label.rating', ['stars' => e($view->data->listing->rating), 'reviews' => e($view->data->listing->review_count)], (int) $view->data->listing->review_count); ?>
                                </span>
                            </span>
                        </p>
                    <?php } ?>
                <?php } ?>
                <?php if (null !== $view->data->listing->type->localizable && null !== $view->data->listing->get('_address')) { ?>
                    <div class="d-flex align-items-baseline">
                        <div>
                            <i class="fas fa-map-marker-alt text-danger fa-fw"></i>
                        </div>
                        <div class="ml-2 text-secondary">
                            <?php echo $view->data->listing->getOutputableValueWithSchema('_address'); ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="col-12 col-lg-4 text-center text-lg-right mt-5 mt-lg-0">
                <?php if (null !== $view->data->listing->get('_page')) { ?>
                    <a href="<?php echo route($view->data->listing->type->slug . '/' . $view->data->listing->slug); ?>" class="btn btn-light btn-sm ml-1 mb-2"><i class="far fa-eye"></i> <?php echo e(__('listing.header.label.view')); ?></a>
                    <?php if (null !== $view->settings->sharing) { ?>
                    <div class="share-button btn btn-light btn-sm ml-1 mb-2 position-relative"><i class="fas fa-share-alt"></i>
                        <?php echo e(__('listing.header.label.share')); ?>
                        <div class="share-popup-screen"></div>
                        <div class="share-popup-wrapper">
                            <ul class="list-inline share-popup shadow-md">
                                <li class="list-inline-item">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(route($view->data->listing->type->slug . '/' . $view->data->listing->slug)); ?>" class="share-link btn btn-circle btn-icn btn-facebook" target="_blank" title="<?php echo e(__('listing.header.label.share_facebook')); ?>">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://twitter.com/intent/tweet?text=<?php echo $view->data->listing->getOutputableValue('_title'); ?>&url=<?php echo e(route($view->data->listing->type->slug . '/' . $view->data->listing->slug)); ?>" class="share-link btn btn-circle btn-icn btn-twitter" target="_blank" title="<?php echo e(__('listing.header.label.share_twitter')); ?>">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo route($view->data->listing->type->slug . '/' . $view->data->listing->slug); ?>" class="share-link btn btn-circle btn-icn btn-linkedin-in" target="_blank" title="<?php echo e(__('listing.header.label.share_linkedin')); ?>">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://pinterest.com/pin/create/button/?url=<?php echo e(route($view->data->listing->type->slug . '/' . $view->data->listing->slug)); ?>" class="share-link btn btn-circle btn-icn btn-pinterest" target="_blank" title="<?php echo e(__('listing.header.label.share_pinterest')); ?>">
                                        <i class="fab fa-pinterest"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://www.reddit.com/submit?title=<?php echo $view->data->listing->getOutputableValue('_title'); ?>&url=<?php echo e(route($view->data->listing->type->slug . '/' . $view->data->listing->slug)); ?>" class="share-link btn btn-circle btn-icn btn-reddit" target="_blank" title="<?php echo e(__('listing.header.label.share_reddit')); ?>">
                                        <i class="fab fa-reddit"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if (null !== $view->settings->bookmarking) { ?>
                        <?php if (false === auth()->check()) { ?>
                            <a href="<?php echo route('account/login'); ?>" class="btn btn-light btn-sm ml-1 mb-2"><?php echo view('misc/bookmark', ['type' => 'button']); ?></a>
                        <?php } else { ?>
                            <span class="bookmark-button btn btn-light btn-sm ml-1 mb-2" data-action="bookmark" data-type="button" data-id="<?php echo $view->data->listing->id; ?>" data-url="<?php echo route('ajax/bookmark'); ?>">
                                <?php echo view('misc/bookmark', ['type' => 'button', 'state' => ($view->data->bookmarks->contains('listing_id', $view->data->listing->id)) ? 'on' : 'off']); ?>
                            </span>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
                <?php if (null !== $view->data->listing->type->reviewable && null !== $view->data->listing->get('_reviews')) { ?>
                    <?php if ($view->data->listing->review_count > 0) { ?>
                        <div class="btn-group ml-1 mb-2" role="group">
                            <a href="<?php echo route($view->data->listing->type->slug . '/' . $view->data->listing->slug . '/reviews'); ?>" class="btn btn-light btn-sm"><i class="far fa-star"></i> <?php echo e(__('listing.header.label.reviews')); ?></a>
                            <a href="<?php echo route($view->data->listing->type->slug . '/' . $view->data->listing->slug . '/add-review'); ?>" class="btn btn-light btn-sm"><i class="fas fa-plus"></i></a>
                        </div>
                    <?php } else { ?>
                        <a href="<?php echo route($view->data->listing->type->slug . '/' . $view->data->listing->slug . '/add-review'); ?>" class="btn btn-light btn-sm ml-1 mb-2"><i class="far fa-star"></i> <?php echo e(__('listing.header.label.reviews_add')); ?></a>
                    <?php } ?>
                <?php } ?>
                <?php if (null === $view->data->listing->claimed) { ?>
                    <a href="<?php echo route($view->data->listing->type->slug . '/' . $view->data->listing->slug . '/claim'); ?>" class="btn btn-light btn-sm ml-1 mb-2"><i class="fas fa-shield-alt"></i> <?php echo e(__('listing.header.label.claim')); ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
</section>
<?php
    layout()->addFooterJs('<script>
    $(document).ready(function() {
        $(\'.share-button\').on(\'click\', function (event) {
            event.preventDefault();
            var popup = $(this).find(\'.share-popup-wrapper\');
            var screen = $(this).find(\'.share-popup-screen\');

            if (typeof popup !== \'undefined\' && typeof screen !== \'undefined\') {
                screen.show();
                popup.show();
            }

            $(document).on(\'keydown\', function (event) {
                if (screen.is(\':visible\') && event.keyCode === 27) {
                    screen.hide();
                    popup.hide();
                }
            });

            $(document).on(\'click\', \'.share-popup-screen\', function (event) {
                event.preventDefault();
                screen.hide();
                popup.hide();
            });
        });

        $(\'.share-link\').on(\'click\', function(event) {
            event.preventDefault();
            window.open($(this).attr(\'href\'), \'\', \'height=450, width=550, top=\' + ($(window).height() / 2 - 275) + \', left=\' + ($(window).width() / 2 - 225) + \', toolbar=0, location=0, menubar=0, directories=0\');

            return false;
        });
    });
</script>');
?>
