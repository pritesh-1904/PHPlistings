<?php 
    if (0 !== $columns = $view->settings->about + $view->settings->menu + $view->settings->contact + $view->settings->social) {
        $width = 12 / $columns;
    }
?>
<footer>
    <?php if (0 !== $columns) { ?>
    <div class="widget footer-widget <?php echo $view->settings->colorscheme; ?> py-6">
        <div class="container">
            <div class="row">
                <?php if (null !== $view->settings->about) { ?>
                <div class="col-12 col-lg-<?php echo $width; ?> mb-4 order-<?php echo e($view->settings->about_order); ?>">
                    <h4 class="text-bold display-7 mb-4"><?php echo e($view->settings->about_heading); ?></h4>
                    <p><?php echo d($view->settings->about_paragraph); ?></p>
                </div>
                <?php } ?>
                <?php if (null !== $view->settings->menu) { ?>
                <div class="col-12 col-lg-<?php echo $width; ?> mb-4 order-<?php echo e($view->settings->menu_order); ?>">
                    <h4 class="text-bold display-7 mb-4"><?php echo e($view->settings->menu_heading); ?></h4>
                    <ul class="list-unstyled">
                    <?php 
                        $items = \App\Models\WidgetMenuGroup::find($view->settings->menu_group)->getTree();

                        foreach ($items as $item) {
                            echo '<li class="mb-2' . (null !== $item->get('highlighted') ? ' highlighted-item' : '') . '"><a href="' . $item->getLink() . '" target="' . e($item->target) . '"' . ($item->nofollow == '1' ? ' rel="nofollow"' : '') . '>' . e($item->name) . '</a></li>';
                        }
                    ?>
                    </ul>
                </div>
                <?php } ?>
                <?php if (null !== $view->settings->contact) { ?>
                <div class="col-12 col-lg-<?php echo $width; ?> mb-4 order-<?php echo e($view->settings->contact_order); ?>">
                    <h4 class="text-bold display-7 mb-4"><?php echo e($view->settings->contact_heading); ?></h4>
                    <p><?php echo d($view->settings->contact_paragraph); ?></p>
                    <ul class="list-unstyled display-11">
                        <?php if ('' != $view->settings->contact_address) { ?>
                        <li class="py-2">
                            <div class="d-flex align-items-baseline">
                                <div>
                                    <i class="fas fa-map-marker-alt text-danger fa-fw"></i>
                                </div>
                                <div class="ml-2">
                                    <?php echo d($view->settings->contact_address); ?>
                                </div>
                            </div>
                        </li>
                        <?php } ?>
                        <?php if ('' != $view->settings->contact_phone) { ?>
                        <li class="py-2">
                            <div class="d-flex align-items-baseline">
                                <div>
                                    <i class="fas fa-phone-alt fa-fw"></i>
                                </div>
                                <div class="ml-2">
                                    <?php echo d($view->settings->contact_phone); ?>
                                </div>
                            </div>
                        </li>
                        <?php } ?>
                    </ul>
                 </div>
                <?php } ?>
                <?php if (null !== $view->settings->social) { ?>
                <div class="col-12 col-lg-<?php echo $width; ?> mb-4 order-<?php echo e($view->settings->social_order); ?>">
                    <h4 class="text-bold display-7 mb-4"><?php echo e($view->settings->social_heading); ?></h4>
                    <p><?php echo d($view->settings->social_paragraph); ?></p>
                    <?php if ('' != $view->settings->social_facebook) { ?>
                        <a rel="nofollow" target="_blank" href="<?php echo e($view->settings->social_facebook); ?>" class="btn btn-circle btn-icn btn-facebook mb-2" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <?php } ?>
                    <?php if ('' != $view->settings->social_twitter) { ?>
                        <a rel="nofollow" target="_blank" href="<?php echo e($view->settings->social_twitter); ?>" class="btn btn-circle btn-icn btn-twitter mb-2" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <?php } ?>
                    <?php if ('' != $view->settings->social_instagram) { ?>
                        <a rel="nofollow" target="_blank" href="<?php echo e($view->settings->social_instagram); ?>" class="btn btn-circle btn-icn btn-instagram mb-2" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <?php } ?>
                    <?php if ('' != $view->settings->social_linkedin) { ?>
                        <a rel="nofollow" target="_blank" href="<?php echo e($view->settings->social_linkedin); ?>" class="btn btn-circle btn-icn btn-linkedin-in mb-2" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <?php } ?>
                    <?php if ('' != $view->settings->social_youtube) { ?>
                        <a rel="nofollow" target="_blank" href="<?php echo e($view->settings->social_youtube); ?>" class="btn btn-circle btn-icn btn-youtube mb-2" title="YouTube"><i class="fab fa-youtube"></i></a>
                    <?php } ?>
                    <?php if ('' != $view->settings->social_vimeo) { ?>
                        <a rel="nofollow" target="_blank" href="<?php echo e($view->settings->social_vimeo); ?>" class="btn btn-circle btn-icn btn-vimeo mb-2" title="Vimeo"><i class="fab fa-vimeo-v"></i></a>
                    <?php } ?>
                    <?php if ('' != $view->settings->social_flickr) { ?>
                        <a rel="nofollow" target="_blank" href="<?php echo e($view->settings->social_flickr); ?>" class="btn btn-circle btn-icn btn-flickr mb-2" title="Flickr"><i class="fab fa-flickr"></i></a>
                    <?php } ?>
                    <?php if ('' != $view->settings->social_pinterest) { ?>
                        <a rel="nofollow" target="_blank" href="<?php echo e($view->settings->social_pinterest); ?>" class="btn btn-circle btn-icn btn-pinterest mb-2" title="Pinterest"><i class="fab fa-pinterest-p"></i></a>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="bg-dark py-4">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center text-white">
                    <p class="text-sm">
                        <?php if ('' != $view->settings->copyright) { ?>
                            <?php echo \str_replace('{year}', date('Y'), d($view->settings->copyright)); ?>
                        <?php } else { ?>
                            Powered by <a href="https://www.phplistings.com" title="Business Directory Software">phpListings.com</a> - Powerful directory software for local business directory websites.
                        <?php } ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
