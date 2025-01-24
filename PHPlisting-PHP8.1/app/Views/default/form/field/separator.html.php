<?php if ('' != $view->getLabel()) { ?>
<ul class="nav nav-tabs mt-1 mb-4">
    <li class="nav-item">
        <span class="nav-link active"><?php echo e($view->getLabel()); ?></span>
    </li>
</ul>
<?php } else { ?>
<ul class="nav nav-tabs my-5"></ul>
<?php } ?>
