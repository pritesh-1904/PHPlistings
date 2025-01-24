<form<?php echo $view->attributes; ?>>
<?php foreach ($view->fields as $name => $field) {
    if (!$field->isAction()) {
        if (!$field->isHidden() && !$field->isSeparator()) { ?>        
            <div class="form-group row">
                <label for="<?php echo $field->getAttributes()->id; ?>" class="col-md-3 col-form-label text-md-right<?php echo $field->isRequired() ? ' text-bold' : ''; ?>">
                    <?php echo e($field->getLabel()); ?>
                </label>
                <div class="col-md-9">
                    <?php echo $field->render(); ?>
                    <?php if (count($field->getErrors()) > 0) { ?>
                        <div class="invalid-feedback">
                            <?php echo implode('<br />', $field->getErrors()); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } else {
            echo $field->render();
        }
    } ?>
<?php } ?>
<div class="form-group row">
    <label for="_actions" class="col-md-3 col-form-label"></label>
    <div class="col-md-9">
        <?php foreach ($view->fields as $name => $field) {
            if ($field->isAction()) {
                echo $field->render();
            }
        } ?>
    </div>
</div>

</form>