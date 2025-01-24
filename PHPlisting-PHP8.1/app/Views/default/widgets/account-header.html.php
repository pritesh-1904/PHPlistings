<?php
    $header = false;

    if ('' != $view->settings->get('image', '') && null !== $file = \App\Models\File::where('document_id', $view->settings->image)->first()) {
        if (false !== $file->isImage()) {
            $header = $file;
        }
    }
    
    if (false !== $header) {
        $attributes = attr([
            'src' => $header->large()->getUrl(),
            'width' => $header->large()->getWidth(),
            'height' => $header->large()->getHeight(),
            'class' => 'bg-img',
            'alt' => e($header->title),
        ]);
    }
?>
<section class="row no-gutters position-relative user-bg-img py-5 duo-fill">
    <?php if (false !== $header) { ?>
        <img <?php echo $attributes; ?> />
    <?php } else { ?>
        <img class="bg-img" src="<?php echo asset('css/default/images/hero-user.jpg'); ?>" alt="" />
    <?php } ?>
    <div class="col-12 d-flex align-items-center justify-content-center">
        <div class="box-overlay user-img text-center">
            <i class="fas fa-user-circle text-white fa-8x"></i>
            <p class="text-medium text-white mt-4 display-6"><?php echo e(auth()->user()->getName()); ?></p>
        </div>
    </div>
</section>
