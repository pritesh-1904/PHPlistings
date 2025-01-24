<?php $view->attributes->append('class', 'form-inline'); ?>
<form<?php echo $view->attributes; ?>>
<?php foreach ($view->fields as $name => $field) { ?>
    <div class="form-group mb-2 mr-sm-2">
    <?php if (!$field->isHidden()) { ?>
        <label for="<?php echo $field->getAttributes()->id; ?>" class="sr-only">
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
    <?php } else {
        echo $field->render();
    } ?>
    </div>
<?php } ?>
</form>