        <div class="table-responsive">
            <table class="table mb-0">
                <tbody>
                    <?php foreach ($view->items as $item) { ?>
                    <tr>
                        <td>
                            <strong><?php echo __('hour.dow.' . $item->dow); ?></strong>
                        </td>
                        <td>
                            <?php echo locale()->formatTime($item->start_time); ?>
                             - 
                            <?php echo locale()->formatTime($item->end_time); ?>
                        </td>
                        <td class="actions">
                            <button type="button" data-id="<?php echo $item->id; ?>" class="btn btn-sm btn-light"><i class="fas fa-times"></i></button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
