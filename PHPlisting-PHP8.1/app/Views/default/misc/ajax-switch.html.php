<?php layout()->addFooterJs("<script>
$(document).ready(function() {
    $('.ajax-switch').confirmation({
        title: '" . e(__('default.confirmation.message')) . "',
        confirmationEvent: 'confirmation',
        btnOkLabel: '" . e(__('default.confirmation.yes')) . "',
        btnCancelLabel: '" . e(__('default.confirmation.no')) . "'
    });    
    
    $('.ajax-switch').on('confirmation', function () {
        if ($(this).data('value') == 1) {
            $(this).html('<i class=\"fas fa-toggle-off fa-2x" . ('rtl' === locale()->getDirection() ? ' fa-flip-horizontal' : '') . "\" style=\"color: #D8DADD;\"></i>');
            $(this).data('value', 0);
        } else {
            $(this).html('<i class=\"fas fa-toggle-on fa-2x" . ('rtl' === locale()->getDirection() ? ' fa-flip-horizontal' : '') . "\" style=\"color: #027BE3;\"></i>');
            $(this).data('value', 1);
        }

        return $.ajax({
            'type': 'POST',
            'url': '" . route('ajax/switch') . "',
            'data': {
                table: $(this).data('table'),
                column: $(this).data('column'),
                id: $(this).data('id'),
                value: $(this).data('value')
            },
            'cache': false,
            'dataType': 'text',
            'success': function(response) {
                if ('' !== response) {
                    $(this).html(response);
                }
            },
            'error': function(jqXHR, error, error_text) {
                alert('Switch Plugin Error, '+jqXHR.responseText+', '+error+', '+error_text);
            },
            'timeout': 5000
        });
    });
});
</script>"); ?>

<?php if ($view->value == 1) { ?>
    <span class="ajax-switch" data-table="<?php echo $view->table; ?>" data-column="<?php echo $view->column; ?>" data-id="<?php echo $view->id; ?>" data-value="1"><i class="fas fa-toggle-on fa-2x<?php echo ('rtl' === locale()->getDirection()) ? ' fa-flip-horizontal' : ''; ?>" style="color: #027BE3;"></i></span>
<?php } else { ?>
    <span class="ajax-switch" data-table="<?php echo $view->table; ?>" data-column="<?php echo $view->column; ?>" data-id="<?php echo $view->id; ?>" data-value="0"><i class="fas fa-toggle-off fa-2x<?php echo ('rtl' === locale()->getDirection()) ? ' fa-flip-horizontal' : ''; ?>" style="color: #D8DADD;"></i></span>
<?php } ?>
