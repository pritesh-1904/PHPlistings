<?php
    $image = false;

    if ('' != $view->settings->get('image', '') && null !== $file = \App\Models\File::where('document_id', $view->settings->image)->first()) {
        if (false !== $file->isImage()) {
            $image = $file;
            
            $attributes = attr([
                'src' => $image->large()->getUrl(),
                'width' => $image->large()->getWidth(),
                'height' => $image->large()->getHeight(),
                'class' => 'img-fluid w-100',
                'alt' => e($view->settings->alt),
            ]);
        }
    }
?>
<?php if (false !== $image) { ?>
<section class="row position-relative p-0 no-gutters">
    <div class="col-12">
        <img <?php echo $attributes; ?> />
        <?php if ('' != $view->settings->get('caption', '')) { ?>
            <div class="widget-image-caption">
                <h3 class="text-bold" style="color: <?php echo e($view->settings->caption_color); ?>;">
                    <?php echo e($view->settings->caption); ?>
                </h3>
            </div>
        <?php } ?>
    </div> 
</section>
<?php } ?>
