<?php
    $image = false;

    if ('' != $view->settings->get('image', '') && null !== $file = \App\Models\File::where('document_id', $view->settings->image)->first()) {
        if (false !== $file->isImage()) {
            $image = $file;
            
            $attributes = attr([
                'src' => $image->large()->getUrl(),
                'width' => $image->large()->getWidth(),
                'height' => $image->large()->getHeight(),
                'class' => 'bg-img',
                'alt' => e($image->title),
            ]);
        }
    }
?>
<section class="position-relative dark-fill-light py-6">
    <?php if (false !== $image) { ?>
        <img <?php echo $attributes; ?> />
    <?php } else { ?>
        <img class="bg-img" src="<?php echo asset('css/default/images/newsletter-bg.jpg'); ?>" alt="" />
    <?php } ?>
    <div class="container">
        <div class="text-white text-center box-overlay py-4">
            <h3 class="text-bold display-1 text-shadow"><?php echo d($view->settings->heading); ?></h3>
            <p class="text-thin display-9 text-shadow l-space-3 my-4"><?php echo d($view->settings->paragraph); ?></p>
            <p><?php echo d($view->settings->button); ?></p>
        </div>
    </div>
</section>
