<?php
    $fields = [
        'title' => ['_title_size' => __('pricing.label.chars')],
        'short_description' => ['_short_description_size' => __('pricing.label.chars')],
        'description' => ['_description_size' => __('pricing.label.chars'), '_description_links_limit' => __('pricing.label.links')],
        'gallery_id' => ['_gallery_size' => __('pricing.label.images')],
    ];
?>
<section class="widget pricing-widget <?php echo $view->settings->colorscheme; ?> py-6">
    <div class="container">
        <div class="row mb-lg-7 mb-5">
            <div class="col-12">
                <p class="text-caption text-black text-uppercase display-11 l-space-1"><?php echo e($view->settings->caption); ?></p>
                <h3 class="text-black display-5 l-space-0"><?php echo e($view->settings->heading); ?></h3>
            </div>
        </div>
        <div class="row pricing pt-lg-2">
            <?php foreach ($view->products as $product) { ?>
            <div class="col-12 col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-md mb-lg-0 mb-5<?php if (null !== $product->featured) echo ' card-hot card-hot-sm'; ?>">
                    <?php if (null != $product->featured) { ?>
                        <div class="card-top bg-primary"></div>
                    <?php } ?>
                    <div class="card-body">
                        <p class="card-title display-5 text-bold text-center my-5"><?php echo e($product->name); ?></p>
                        <?php if (null !== $view->settings->options) { ?>
                            <hr class="mb-5">
                            <?php if ('' != $product->description) { ?>
                            <p class="card-text text-center text-secondary display-11">
                                <?php echo d($product->description); ?>
                            </p>
                            <?php } ?>
                        <?php } ?>
                        
                        <?php if (null !== $view->settings->options) { ?>
                        <ul class="list-unstyled p-3">
                            <li class="d-flex justify-content-between align-items-center py-3">
                                <?php echo e(__('pricing.label.dedicated_page')); ?>
                                <?php if (null !== $product->_page) { ?>
                                    <span><i class="fas fa-check text-success"></i></span>
                                <?php } else { ?>
                                    <span><i class="fas fa-times text-secondary"></i></span>
                                <?php } ?>
                            </li>
                            <li class="d-flex justify-content-between align-items-center py-3">
                                <?php echo e(__('pricing.label.featured_listing')); ?>
                                <?php if (null !== $product->_featured) { ?>
                                    <span><i class="fas fa-check text-success"></i></span>
                                <?php } else { ?>
                                    <span><i class="fas fa-times text-secondary"></i></span>
                                <?php } ?>
                            </li>
                            <li class="d-flex justify-content-between align-items-center py-3">
                                <?php echo e(__('pricing.label.badges')); ?>
                                <?php if ($product->badges()->whereNotNull('active')->count() > 0) { ?>
                                    <span><i class="fas fa-check text-success"></i></span>
                                <?php } else { ?>
                                    <span><i class="fas fa-times text-secondary"></i></span>
                                <?php } ?>
                            </li>
                            <li class="d-flex justify-content-between align-items-center py-3">
                                <?php echo e(__('pricing.label.extra_categories')); ?>
                                <span class="text-secondary text-bold"><?php echo $product->_extra_categories; ?></span>
                            </li>
                            <?php if (null !== $view->type->localizable) { ?>
                                <li class="d-flex justify-content-between align-items-center py-3">
                                    <?php echo e(__('pricing.label.address')); ?>
                                    <?php if (null !== $product->_address) { ?>
                                        <span><i class="fas fa-check text-success"></i></span>
                                    <?php } else { ?>
                                        <span><i class="fas fa-times text-secondary"></i></span>
                                    <?php } ?>
                                </li>
                                <li class="d-flex justify-content-between align-items-center py-3">
                                    <?php echo e(__('pricing.label.map')); ?>
                                    <?php if (null !== $product->_map) { ?>
                                        <span><i class="fas fa-check text-success"></i></span>
                                    <?php } else { ?>
                                        <span><i class="fas fa-times text-secondary"></i></span>
                                    <?php } ?>
                                </li>
                            <?php } ?>
                            <?php if ('Event' == $view->type->type) { ?>
                                <li class="d-flex justify-content-between align-items-center py-3">
                                    <?php echo e(__('pricing.label.event_dates')); ?>
                                    <span class="text-secondary text-bold"><?php echo $product->_event_dates; ?></span>
                                </li>
                            <?php } ?>
                            <li class="d-flex justify-content-between align-items-center py-3">
                                <?php echo e(__('pricing.label.send_message')); ?>
                                <?php if (null !== $product->_send_message) { ?>
                                    <span><i class="fas fa-check text-success"></i></span>
                                <?php } else { ?>
                                    <span><i class="fas fa-times text-secondary"></i></span>
                                <?php } ?>
                            </li>
                            <?php if (null !== $view->type->reviewable) { ?>
                                <li class="d-flex justify-content-between align-items-center py-3">
                                    <?php echo e(__('pricing.label.reviews')); ?>
                                    <?php if (null !== $product->_reviews) { ?>
                                        <span><i class="fas fa-check text-success"></i></span>
                                    <?php } else { ?>
                                        <span><i class="fas fa-times text-secondary"></i></span>
                                    <?php } ?>
                                </li>
                            <?php } ?>
                            <li class="d-flex justify-content-between align-items-center py-3">
                                <?php echo e(__('pricing.label.dofollow')); ?>
                                <?php if (null !== $product->_dofollow) { ?>
                                    <span><i class="fas fa-check text-success"></i></span>
                                <?php } else { ?>
                                    <span><i class="fas fa-times text-secondary"></i></span>
                                <?php } ?>
                            </li>
                            <li class="d-flex justify-content-between align-items-center py-3">
                                <?php echo e(__('pricing.label.seo')); ?>
                                <?php if (null !== $product->_seo) { ?>
                                    <span><i class="fas fa-check text-success"></i></span>
                                <?php } else { ?>
                                    <span><i class="fas fa-times text-secondary"></i></span>
                                <?php } ?>
                            </li>
                            <li class="d-flex justify-content-between align-items-center py-3">
                                <?php echo e(__('pricing.label.backlink')); ?>
                                <?php if (null !== $product->_backlink) { ?>
                                    <span><i class="fas fa-check text-success"></i></span>
                                <?php } else { ?>
                                    <span><i class="fas fa-times text-secondary"></i></span>
                                <?php } ?>
                            </li>
                            <?php if (null !== $view->settings->fields) { ?>
                                <?php foreach ($product->fields as $field) { ?>
                                <li class="d-flex justify-content-between align-items-center py-3">
                                    <?php echo e($field->label); ?>
                                    <?php if (array_key_exists($field->name, $fields)) { ?>
                                        <?php $options = []; ?>
                                        <?php foreach ($fields[$field->name] as $key => $value) { ?>
                                            <?php $options[] = '<span class="text-black">' . $product->get($key) . '</span> ' . $value; ?>
                                        <?php } ?>
                                        <span class="text-secondary text-right"><?php echo implode('<br />', $options); ?></span>
                                    <?php } else { ?>
                                        <span><i class="fas fa-check text-success"></i></span>
                                    <?php } ?>
                                </li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                        <?php } ?>
                        <?php foreach ($product->pricings as $pricing) { ?>
                        <hr class="my-5">
                        <p class="display-3 text-black text-center mt-5 mb-2"><?php echo (0 < $pricing->price ? locale()->formatPrice($pricing->price) : __('widget.pricing.label.free')); ?></p>
                        <?php if (0 < $pricing->price) { ?>
                            <p class="text-center"><?php echo e($pricing->getNameWithoutPrice()); ?></p>
                        <?php } ?>
                        <p class="text-center">
                            <a class="btn btn-round btn-primary" href="<?php echo route('account/manage/' . $view->type->slug . '/create', ['pricing_id' => $pricing->id]); ?>"><?php echo e(__('pricing.label.order_now')); ?></a>
                        </p>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</section>
