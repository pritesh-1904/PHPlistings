<?php
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
<section class="widget searchbar-widget <?php echo $view->settings->colorscheme; ?> py-6">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-11 mx-auto">
                <div class="search-form-top bg-white p-3 p-lg-1 pl-lg-4">
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
            </div>
        </div>
    </div>              
</section>
