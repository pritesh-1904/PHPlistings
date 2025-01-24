<?php
    $size = [
        'small' => 'modal-sm',
        'medium' => '',
        'large' => 'modal-lg',
    ];
?>
<div id="popup-widget-<?php echo $view->id; ?>" class="widget popup-widget modal fade" tabindex="-1" style="opacity: 0.9;">
    <div class="modal-dialog <?php echo $size[$view->settings->size]; ?> modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h3 class="modal-title display-10 text-black"><?php echo e($view->settings->title); ?></h3>
                <?php if (null !== $view->settings->closable) { ?>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times-circle"></i></span>
                </button>
                <?php } ?>
            </div>
            <div class="modal-body">
                <?php echo d($view->settings->description); ?>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    <?php if (null === $view->settings->recurring) { ?>
    if (undefined !== Cookies.get('_popup_widget_<?php echo $view->id; ?>')) {
        return;
    }
    <?php } ?>
    setTimeout(function() {
        $("#popup-widget-<?php echo $view->id; ?>").modal(
            <?php if (null === $view->settings->closable) { ?>
                {
                    backdrop: 'static',
                    keyboard: false
                }
            <?php } ?>
        );
        $("#popup-widget-<?php echo $view->id; ?>").on('hidden.bs.modal', function () {
            Cookies.set('_popup_widget_<?php echo $view->id; ?>', 1, { path: '<?php echo '/' . trim(request()->basePath(), '/'); ?>' });
        });
    }, <?php echo $view->settings->delay * 1000; ?>);
});
</script>
