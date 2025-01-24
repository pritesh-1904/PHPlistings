<div class="py-3"><?php echo $view->data->links('misc/pagination'); ?></div>
<?php $id = rand(1,999); ?>
<?php if (count($view->bulkActions) > 0) { ?>
<form id="datatable-<?php echo $id; ?>" method="post">
<?php } ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <?php if (null !== $view->sortable) { ?>
                        <th class="border-top-0 border-bottom" scope="col"></th>
                    <?php } ?>
                    <?php if (count($view->bulkActions) > 0) { ?>
                        <th class="border-top-0 border-bottom" scope="col"><input type="checkbox" class="_checkbox_select_all"></th>
                    <?php } ?>
                    <?php foreach ($view->columns as $key => $column) { ?>
                        <th class="border-top-0 border-bottom" scope="col">
                            <?php if (in_array($key, $view->orderColumns)) {
                                $direction = 'asc';
                                if (null !== request()->get->get('sort') && request()->get->get('sort') == $key) {
                                    if (null !== request()->get->get('sort_direction')) {
                                        $direction = (request()->get->get('sort_direction') == 'asc') ? 'desc' : 'asc';
                                    } ?>
                                    <a href="<?php echo request()->urlWithQuery(['sort' => $key, 'sort_direction' => $direction, 'page' => null]); ?>"><?php echo e($column[0]); ?> <i class="fas fa-<?php echo ($direction == 'asc') ? 'sort-amount-down' : 'sort-amount-down-alt'; ?>"></i></a>
                                <?php } else { ?>
                                    <a href="<?php echo request()->urlWithQuery(['sort' => $key, 'sort_direction' => 'asc', 'page' => null]); ?>"><?php echo e($column[0]); ?></a>
                                <?php } ?>
                            <?php } else { ?>
                                <?php echo e($column[0]); ?>
                            <?php } ?>
                        </th>
                    <?php } ?>
                    <?php if (count($view->actions) > 0) { ?>
                        <th class="border-top-0 border-bottom" scope="col"><?php echo e(__('default.datatable.label.actions')); ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody id="datatable-<?php echo $id; ?>-sortable">
                <?php foreach($view->data as $record) { ?>
                <tr>
                    <?php if (null !== $view->sortable) { ?>
                        <td class="draggable border-top-0 border-bottom" data-id="<?php $sortableId = $view->sortableId; echo ($sortableId instanceof \Closure) ? $sortableId($record) : $record->id; ?>" style="cursor: move;"><i class="fas fa-arrows-alt-v"></i></td>
                    <?php } ?>
                    <?php if (count($view->bulkActions) > 0) { ?>
                        <td class="border-top-0 border-bottom"><input type="checkbox" name="id[]" value="<?php $bulkActionsId = $view->bulkActionsId; echo ($bulkActionsId instanceof \Closure) ? $bulkActionsId($record) : $record->id; ?>"></td>
                    <?php } ?>

                    <?php foreach ($view->columns as $key => $column) { ?>
                        <td class="border-top-0 border-bottom">
                            <?php
                            if (isset($column[1])) {
                                if ($column[1] instanceof \Closure) {
                                    echo $column[1]($record);
                                } else {
                                    echo $column[1];
                                }
                            } else {
                                echo $record->get($key);
                            }
                            ?>
                        </td>
                    <?php } ?>
                    <?php if (count($view->actions) > 0) { ?>
                        <td class="actions border-top-0 border-bottom">
                            <?php foreach ($view->actions as $key => $action) { ?>
                                <?php $link = $action[1]($record); if (null !== $link) { ?>
                                    <a class="btn btn-sm <?php echo ($key == 'delete' ? 'btn-danger' : 'btn-light'); ?> mb-1"<?php if ($key == 'delete') echo ' data-toggle="confirmation" data-title="' .  e(__('default.confirmation.message')) . '" data-btn-ok-label="' . e(__('default.confirmation.yes')) . '" data-btn-cancel-label="' . e(__('default.confirmation.no')) . '"'; ?> href="<?php echo $link; ?>"<?php echo (isset($action[2]) && false !== ($action[2])) ? ' target="_blank"' : ''; ?>><?php echo (($action[0] instanceof \Closure) ? $action[0]($record) : $action[0]); ?></a>
                                <?php } ?>
                            <?php } ?>                                    
                        </td>
                    <?php } ?>

                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if (count($view->bulkActions) > 0) { ?>
        <div class="form-row align-items-center mt-4">
            <div class="col-auto">
                <?php echo (new \App\Src\Form\Type\Select('action', ['options' => ['' => e(__('default.datatable.label.bulk_actions'))] + $view->bulkActions]))->render(); ?>
            </div>
            <div class="col-auto">
                <?php echo (new \App\Src\Form\Type\Submit('submit'))->render(); ?>
            </div>
        </div>
    <?php } ?>

<?php if (count($view->bulkActions) > 0) { ?>
</form>
<?php } ?>

<div class="py-3"><?php echo $view->data->links('misc/pagination'); ?></div>

<script>
    $(document).ready(function() {
        <?php if (count($view->bulkActions) > 0) { ?>
        $('._checkbox_select_all').on('click', function (event) {
            var checked = $(this).is(':checked');

            $(this).closest('form').find('input[type="checkbox"][name="id[]"]').each(function () {
                this.checked = (checked) ? true : false;
            });
        });
        <?php } ?>
        <?php if (null !== $view->sortable) { ?>
        $('#datatable-<?php echo $id; ?>-sortable').sortable({
            'url': '<?php echo route('ajax/sort'); ?>',
            'source': '<?php echo $view->sortable; ?>',
            'data': '<?php echo $view->sortableData; ?>',
            'speed': 200
        });
        <?php } ?>
    });
</script>
