<?php if (false !== $view->badge->image->isImage()) { 
        $attributes = attr([
            'src' => $view->badge->image->medium()->getUrl(),
            'width' => $view->badge->image->medium()->getWidth(),
            'height' => $view->badge->image->medium()->getHeight(),
            'class' => 'listing-badge',
            'title' => e($view->badge->name),
            'alt' => e($view->badge->image->title),
        ]);
?>
    <img <?php echo $attributes; ?> />
<?php } ?>