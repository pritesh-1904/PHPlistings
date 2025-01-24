<?php
    $removeButtons = [];
    $removePlugins = [];
    
    layout()->addJs('<script src="' . asset('js/ckeditor/ckeditor.js?v=419107') . '"></script>');

    $view->attributes->append('class', 'form-control');

    $view->attributes->add('name', e($view->name));

    if (count($view->errors) > 0) {
        $view->attributes->append('class', 'is-invalid');
    }
    if (isset($view->description)) {
        $view->attributes->add('aria-describedby', e($view->attributes->id) . '-description');
    }

    foreach($view->getConstraints() as $constraint) {
        if ($constraint instanceof \App\Src\Validation\HtmlmaxlengthValidator) {
            $view->attributes->add('maxlength', $constraint->getParameter());
        }
    }

    foreach($view->getConstraints() as $constraint) {
        if ('advanced' != $view->get('config') && $constraint instanceof \App\Src\Validation\HtmlmaxtagsValidator) {
            if ('a' == $constraint->getTagParameter() && 0 == $constraint->getValueParameter()) {
                $removeButtons[] = 'Link,Unlink';
            }
        }
    }

    if (false === auth()->check() || '' == config()->other->get('openai_api_key', '') || 1 > config()->other->get('openai_daily_limit', 0)) {
        $removePlugins[] = 'cowriter';
    }

?>
<div class="cke_<?php echo locale()->getDirection(); ?>"><textarea<?php echo $view->attributes; ?>><?php echo $view->value; ?></textarea></div>
<script>
    $(document).ready( function () {
        CKEDITOR.timestamp = '419107';
        CKEDITOR.aiAjaxUrl = '<?php echo route('ajax/ai'); ?>';

        var editor_<?php echo e($view->attributes->id); ?> = CKEDITOR.replace( '<?php echo e($view->attributes->id); ?>', {
            language: '<?php echo locale()->getLocale(); ?>',
            customConfig: '<?php echo null !== $view->get('config') && 'advanced' == $view->get('config') ? 'config_adv.js?v=419107' : 'config.js?v=419107'; ?>',
            uploadUrl: '<?php echo route('ajax/uploadasset'); ?>',
            filebrowserUploadUrl: '<?php echo route('ajax/uploadasset'); ?>',
            fillEmptyBlocks: false,
            removeButtons: '<?php echo implode(',', $removeButtons); ?>',
            removePlugins: '<?php echo implode(',', $removePlugins); ?>'
        });

<?php if (false !== $view->attributes->has('maxlength')) { ?>
        editor_<?php echo e($view->attributes->id); ?>.on('instanceReady', function() {
            $('#<?php echo e($view->attributes->id); ?>').count({
                'editor': editor_<?php echo e($view->attributes->id); ?>,
                'limit': '<?php echo (int) $view->attributes->maxlength; ?>',
                'template': '<?php echo e(__('form.label.characters_left')); ?>'
            });
        });
<?php } ?>
    });
</script>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-counter"></small>
<?php if (isset($view->description)) { ?>
<small class="form-text text-muted" id="<?php echo e($view->attributes->id); ?>-description"><?php echo e($view->description); ?></small>
<?php } ?>
