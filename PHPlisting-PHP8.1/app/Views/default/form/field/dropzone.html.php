<?php 
    layout()->addCss('<link href="' . asset('js/dropzone/dropzone.css') . '" rel="stylesheet">');
    layout()->addJs('<script src="' . asset('js/dropzone/dropzone.min.js') . '"></script>');
    layout()->addCss('<link href="' . asset('js/cropper/cropper.css') . '" rel="stylesheet">');
    layout()->addJs('<script src="' . asset('js/cropper/cropper.min.js') . '"></script>');

    $view->attributes->add('name', e($view->name));
    $view->attributes->add('type', 'hidden');
    $view->attributes->add('value', $view->value);

    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', e($view->attributes->id) . '-description');
    }

    $type = \App\Models\UploadType::find($view->upload_id);
?>
<div id="<?php echo e($view->attributes->id); ?>_container" class="dropzone"></div>
<input<?php echo $view->attributes; ?>>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
<?php 
    layout()->addFooterJs('<script type="text/javascript">
    $(document).ready(function() {
        $(\'#' . e($view->attributes->id) . '\').dropzone({
            \'url\': \'' . route ('ajax/upload') . '\',
            \'typeId\': \'' . e($type->id) . '\',
            \'maxFiles\': \'' . e($type->max_files) . '\',
            \'userCrop\': ' . (($type->small_image_resize_type == '1' || $type->medium_image_resize_type == '1' || $type->large_image_resize_type == '1') ? 'true' : 'false') . ',
            \'thumbnailWidth\': \'' . e($type->small_image_width) . '\',
            \'thumbnailHeight\': \'' . (($type->small_image_resize_type == '1') ? round($type->small_image_width / $type->cropbox_width * $type->cropbox_height) : e($type->small_image_height)) . '\',
            \'cropBoxWidth\': \'' . e($type->cropbox_width) . '\',
            \'cropBoxHeight\': \'' . e($type->cropbox_height) . '\',
            \'dictDefaultMessage\': \'' . __('upload.label.default_message') . '\',
            \'dictButtonClose\': \'' . __('upload.label.close') . '\',
            \'dictButtonUpload\': \'' . __('upload.label.upload') . '\',
            \'dictButtonUpdate\': \'' . __('upload.label.update') . '\',
            \'dictButtonCrop\': \'' . __('upload.label.crop') . '\',
            \'dictButtonDescription\': \'' . __('upload.label.description') . '\',
            \'dictButtonRemove\': \'' . __('upload.label.remove') . '\',
            \'dictButtonZoomIn\': \'' . __('upload.label.zoomin') . '\',
            \'dictButtonZoomOut\': \'' . __('upload.label.zoomout') . '\',
            \'dictButtonRotate\': \'' . __('upload.label.rotate') . '\',
            \'dictButtonReset\': \'' . __('upload.label.reset') . '\',
            \'dictErrorLimitReached\': \'' . __('upload.alert.limit_perfield_reached') . '\',
        });
    });
</script>');
?>
