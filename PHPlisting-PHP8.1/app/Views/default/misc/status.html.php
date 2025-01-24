<?php if ($view->type == 'published') { ?>
    <?php if ($view->status == '1') { ?>
        <span class="badge badge-success"><?php echo __('status.label.published'); ?></span>
    <?php } else { ?>
        <span class="badge badge-warning"><?php echo __('status.label.pending'); ?></span>
    <?php } ?>
<?php } else if ($view->type == 'user') { ?>
    <?php if ($view->status == '1') { ?>
        <span class="badge badge-success"><?php echo __('status.label.approved'); ?></span>
    <?php } else { ?>
        <span class="badge badge-warning"><?php echo __('status.label.pending'); ?></span>
    <?php } ?>
<?php } else if ($view->type == 'listing') { ?>
    <?php if ($view->status == '1') { ?>
        <span class="badge badge-success"><?php echo __('status.label.approved'); ?></span>
    <?php } else { ?>
        <span class="badge badge-warning"><?php echo __('status.label.pending'); ?></span>
    <?php } ?>
<?php } else if ($view->type == 'order') { ?>
    <?php if ($view->status == 'active') { ?>
        <span class="badge badge-success"><?php echo __('status.label.active'); ?></span>
    <?php } else if ($view->status == 'suspended') { ?>
        <span class="badge badge-danger"><?php echo __('status.label.suspended'); ?></span>
    <?php } else if ($view->status == 'cancelled') { ?>
        <span class="badge badge-danger"><?php echo __('status.label.cancelled'); ?></span>
    <?php } else { ?>
        <span class="badge badge-warning"><?php echo __('status.label.pending'); ?></span>
    <?php } ?>
<?php } else if ($view->type == 'invoice') { ?>
    <?php if ($view->status == 'paid') { ?>
        <span class="badge badge-success"><?php echo __('status.label.paid'); ?></span>
    <?php } else if ($view->status == 'cancelled') { ?>
        <span class="badge badge-danger"><?php echo __('status.label.cancelled'); ?></span>
    <?php } else { ?>
        <span class="badge badge-warning"><?php echo __('status.label.pending'); ?></span>
    <?php } ?>
<?php } else if ($view->type == 'transaction') { ?>
    <?php if ($view->status == 'paid') { ?>
        <span class="badge badge-success"><?php echo __('status.label.paid'); ?></span>
    <?php } else if ($view->status == 'cancelled') { ?>
        <span class="badge badge-danger"><?php echo __('status.label.cancelled'); ?></span>
    <?php } else if ($view->status == 'failed') { ?>
        <span class="badge badge-danger"><?php echo __('status.label.failed'); ?></span>
    <?php } else { ?>
        <span class="badge badge-warning"><?php echo __('status.label.pending'); ?></span>
    <?php } ?>
<?php } else if ($view->type == 'claim') { ?>
    <?php if ($view->status == 'approved') { ?>
        <span class="badge badge-success"><?php echo __('status.label.approved'); ?></span>
    <?php } else if ($view->status == 'rejected') { ?>
        <span class="badge badge-danger"><?php echo __('status.label.rejected'); ?></span>
    <?php } else { ?>
        <span class="badge badge-warning"><?php echo __('status.label.pending'); ?></span>
    <?php } ?>
<?php } else if ($view->type == 'review') { ?>
    <?php if ($view->status == '1') { ?>
        <span class="badge badge-success"><?php echo __('status.label.approved'); ?></span>
    <?php } else { ?>
        <span class="badge badge-warning"><?php echo __('status.label.pending'); ?></span>
    <?php } ?>
<?php } else if ($view->type == 'message') { ?>
    <?php if ($view->status == '1') { ?>
        <span class="badge badge-success"><?php echo __('status.label.approved'); ?></span>
    <?php } else { ?>
        <span class="badge badge-warning"><?php echo __('status.label.pending'); ?></span>
    <?php } ?>
<?php } else if ($view->type == 'email') { ?>
    <?php if ($view->status == 'sent') { ?>
        <span class="badge badge-success"><?php echo __('status.label.sent'); ?></span>
    <?php } else if ($view->status == 'queued') { ?>
        <span class="badge badge-danger"><?php echo __('status.label.queued'); ?></span>
    <?php } else if ($view->status == 'failed') { ?>
        <span class="badge badge-danger"><?php echo __('status.label.failed'); ?></span>
    <?php } else { ?>
        <span class="badge badge-warning"><?php echo __('status.label.pending'); ?></span>
    <?php } ?>
<?php } else if ($view->type == 'task') { ?>
    <?php if ($view->status == '1') { ?>
        <span class="badge badge-danger"><?php echo __('status.label.locked'); ?></span>
    <?php } else { ?>
        <span class="badge badge-success"><?php echo __('status.label.unlocked'); ?></span>
    <?php } ?>
<?php } else if ($view->type == 'export') { ?>
    <?php if ($view->status == 'done') { ?>
        <span class="badge badge-success"><?php echo __('status.label.done'); ?></span>
    <?php } else if ($view->status == 'queued') { ?>
        <span class="badge badge-info"><?php echo __('status.label.queued'); ?></span>
    <?php } else if ($view->status == 'running') { ?>
        <span class="badge badge-warning"><?php echo __('status.label.running'); ?></span>
    <?php } ?>
<?php } else if ($view->type == 'import') { ?>
    <?php if ($view->status == 'done') { ?>
        <span class="badge badge-success"><?php echo __('status.label.done'); ?></span>
    <?php } else if ($view->status == 'queued') { ?>
        <span class="badge badge-info"><?php echo __('status.label.queued'); ?></span>
    <?php } else if ($view->status == 'running') { ?>
        <span class="badge badge-warning"><?php echo __('status.label.running'); ?></span>
    <?php } ?>
<?php } else if ($view->type == 'httpcode') { ?>
    <?php if ($view->status == '200') { ?>
        <span class="badge badge-success">200</span>
    <?php } else if ($view->status == '0') { ?>
        <span class="badge badge-warning"><?php echo __('status.label.unknown'); ?></span>
    <?php } else { ?>
        <span class="badge badge-danger"><?php echo $view->status; ?></span>
    <?php } ?>
<?php } else if ($view->type == 'linkrelation') { ?>
    <?php if ($view->status == 'dofollow') { ?>
        <span class="badge badge-success">DoFollow</span>
    <?php } else if ($view->status == 'nofollow') { ?>
        <span class="badge badge-info">NoFollow</span>
    <?php } else { ?>
        <span class="badge badge-warning"><?php echo __('status.label.unknown'); ?></span>
    <?php } ?>
<?php } ?>
