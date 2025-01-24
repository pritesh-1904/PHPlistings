<?php
    $header = false;

    if ('' != $view->settings->get('header', '') && null !== $file = \App\Models\File::where('document_id', $view->settings->header)->first()) {
        if (false !== $file->isImage()) {
            $header = $file;
        }
    }

    if (false !== $header) {
        $attributes = attr([
            'src' => $header->large()->getUrl(),
            'width' => $header->large()->getWidth(),
            'height' => $header->large()->getHeight(),
            'class' => 'img-fluid w-100',
            'alt' => e($header->title),
        ]);
    }
?>
<?php if (false !== $header) { ?>
<section class="row no-gutters">
    <div class="col-12">
        <img <?php echo $attributes; ?> />
    </div> 
</section>
<?php } ?>
<section class="widget listing-search-results-header-default-widget <?php echo $view->settings->colorscheme; ?> py-6">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-bold">
                    <?php echo e($view->settings->heading); ?>
                </h1>
                <?php if ('' != $view->settings->description) { ?>
                <p class="text-thin display-9 mt-4">
                    <?php echo d($view->settings->description); ?>
                </p>
                <?php } ?>
                <?php if ($view->children->count() > 0) { ?>
                <div class="mt-4">
                    <?php foreach ($view->children as $child) { ?>
                        <a href="<?php echo request()->urlWithQuery(['category_id' => $child->id, 'page' => null]); ?>" class="btn btn-round mb-sm-2 btn-outline-primary mr-1 mb-2" role="button"><?php echo e($child->name); ?></a>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>
