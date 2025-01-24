<section class="widget quad-box-teaser-widget <?php echo $view->settings->colorscheme; ?> py-6">
    <div class="container">
        <div>
            <p class="text-caption text-uppercase text-black l-space-1 display-11"><?php echo e($view->settings->caption); ?></p>
            <h3 class="text-black l-space-0 display-5"><?php echo e($view->settings->heading); ?></h3>
        </div>
        <div class="row mt-3">
        <?php foreach (['first', 'second', 'third', 'fourth'] as $id) { ?>
                <?php 
                    $image = null;

                    $empty = false;

                    if ('' == $view->settings->getTranslation($id . '_heading') && '' == $view->settings->getTranslation($id . '_paragraph')) {
                        $empty = true;
                    }

                    if ('' !== $view->settings->get($id . '_image')) {
                        $image = \App\Models\File::where('document_id', $view->settings->get($id . '_image'))->first();
                    }
                ?>
                <?php if ((null !== $image && false !== $image->isImage()) || false === $empty) { ?>
                    <div class="col-12 col-md-6 col-lg-3 my-2 my-lg-4">
                        <div class="card shadow-md border-0 h-100">
                            <?php if (null !== $image && false !== $image->isImage()) { ?>
                                <?php
                                    $attributes = attr([
                                        'src' => $image->medium()->getUrl(),
                                        'width' => $image->medium()->getWidth(),
                                        'height' => $image->medium()->getHeight(),
                                        'class' => 'card-img-top',
                                        'alt' => e($image->title),
                                    ]);
                                ?>
                                <img <?php echo $attributes; ?> />
                            <?php } ?>
                            <div class="card-body<?php if (null !== $view->settings->center) echo ' text-center'; ?><?php if (false !== $empty) echo ' p-0'; ?>">
                                <?php if (false === $empty) { ?>
                                    <?php if ('' != $view->settings->getTranslation($id . '_heading')) { ?>
                                        <h4 class="text-black l-space-0 display-10 mb-2"><?php echo d($view->settings->getTranslation($id . '_heading')); ?></h4>
                                    <?php } ?>
                                    <p class="display-11 m-0"><?php echo d($view->settings->getTranslation($id . '_paragraph')); ?></p>
                                <?php } ?>
                                <?php if ('' != $view->settings->get($id . '_link')) { ?>
                                    <a href="<?php echo $view->settings->get($id . '_link'); ?>" class="stretched-link"<?php if (null !== $view->settings->nofollow) echo ' rel="nofollow"'; ?><?php if (null !== $view->settings->newwindow) echo ' target="_blank"'; ?>></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</section>
