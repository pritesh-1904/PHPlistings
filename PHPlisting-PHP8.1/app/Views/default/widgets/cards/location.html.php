<?php
    $logo = false;
    
    if ('' != $view->location->logo_id && null !== $view->location->logo && false !== $view->location->logo->isImage()) {
        $logo = $view->location->logo;
    } else if ('' != $view->settings->get('default_logo', '') && null !== $file = \App\Models\File::where('document_id', $view->settings->default_logo)->first()) {
        if (false !== $file->isImage()) {
            $logo = $file;
        }
    }

    if (false !== $logo) {
        $attributes = attr([
            'src' => $logo->small()->getUrl(),
            'width' => $logo->small()->getWidth(),
            'height' => $logo->small()->getHeight(),
            'class' => 'img-fluid w-100 rounded',
            'loading' => 'lazy',
            'alt' => e($logo->title),
        ]);
    }
?>
<a class="card h-100 duo-fill border-0 text-white shadow-md card-m" href="<?php echo route($view->type->slug . '/' . $view->location->slug); ?>">
    <?php if (false !== $logo) { ?>
        <img <?php echo $attributes; ?> />
    <?php } ?>
    <div class="card-img-overlay">
        <h4 class="card-title display-7 m-0 text-medium text-shadow"><?php echo e($view->location->name); ?></h4>
        <?php if (null !== $view->settings->show_description) { ?>
            <p class="display-11 text-shadow"><?php echo e($view->location->short_description); ?></p>
        <?php } ?>
    </div>
</a>
