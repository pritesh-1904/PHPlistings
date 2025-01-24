<form<?php echo $view->attributes; ?>>
<?php foreach ($view->fields as $name => $field) {
    if (!$field->isAction()) {
        if (!$field->isHidden() && !$field->isSeparator()) { ?>
            <div class="form-group">
                <label for="<?php echo $field->getAttributes()->id; ?>">
                    <?php
                        if($field->isRequired()) { 
                            echo '<strong>' . e($field->getLabel()) . '</strong>';
                        } else {
                            echo e($field->getLabel());
                        }
                    ?>
                </label>
                <?php echo $field->render(); ?>
                <?php if (count($field->getErrors()) > 0) { ?>
                    <div class="invalid-feedback">
                        <?php echo implode('<br />', $field->getErrors()); ?>
                    </div>
                <?php } ?>
            </div>
        <?php } else {
            echo $field->render();
        }
    }
} ?>
<div class="form-group">
    <label for="_actions"></label>
    <?php foreach ($view->fields as $name => $field) {
        if ($field->isAction()) {
            echo $field->render();
        }
    } ?>
</div>
</form>