<div class="mb-3">
    <h3><?php echo e(__('admin.settings.heading.index')); ?></h3>
</div>

<ul class="nav nav-pills btn-sm mb-3">
    <?php foreach($view->groups as $group) { ?>
        <li class="nav-item">
            <a class="nav-link<?php echo ($group->slug == $view->currentGroup->slug) ? ' active' : ''; ?>" href="<?php echo adminRoute('settings/' . $group->slug); ?>">
                <?php echo __('admin.settings.group.' . $group->slug); ?>
            </a>
        </li>
    <?php } ?>
</ul>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-md border-0 rounded-0 p-3">
            <div class="card-body">
                <?php echo $view->alert ?? null; ?>
                <?php echo $view->form; ?>
            </div>
        </div>
    </div>
</div>
