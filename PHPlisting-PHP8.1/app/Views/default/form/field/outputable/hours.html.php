    <?php $rand = bin2hex(random_bytes(4)); ?>
    <?php $now = new \DateTime('now', new \DateTimeZone($view->timezone)); ?>
    <?php $active = null; ?>
    <?php $count = 0; ?>
    <?php foreach (locale()->getDaysOfWeek() as $id => $day) { ?>
        <?php if ($view->value->where('dow', $id)->count() > 0) { ?>
            <?php foreach ($view->value->where('dow', $id) as $period) { ?>
                <?php $start = \DateTime::createFromFormat('H:i:s', $period->start_time, new \DateTimeZone($view->timezone)); ?>
                <?php $end = \DateTime::createFromFormat('H:i:s', $period->end_time, new \DateTimeZone($view->timezone)); ?>
                <?php if (null === $active && $now->format('N') == $id && $start <= $now && $end > $now) {$active = $count;} ?>
                <?php $hours[$count] = '<strong>' . $day . ':</strong> ' . locale()->formatTime($period->start_time) . ' - ' . locale()->formatTime($period->end_time); ?>
            <?php } ?>
            <?php $count++; ?>
        <?php } else { ?>
            <?php $hours[$count] = '<strong>' . $day . ':</strong> ' . e(__('listing.label.opening_hours_closed_today')); ?>
            <?php $count++; ?>
        <?php } ?>
    <?php } ?>

    <div class="dropdown d-inline-block">
        <button class="btn <?php echo (null === $active) ? 'btn-outline-secondary' : 'btn-outline-success'; ?> btn-sm dropdown-toggle" type="button" id="_hours_dropdown_<?php echo $rand; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php echo (null !== $active) ? e(__('listing.alert.open_now')) : e(__('listing.alert.closed_now')); ?>
        </button>
        <div class="dropdown-menu" aria-labelledby="_hours_dropdown_<?php echo $rand; ?>">
            <?php foreach ($hours as $id => $hour) { ?>
                <span class="dropdown-item<?php echo (null !== $active && $id == $active) ? ' bg-success text-white' : ''; ?>"><?php echo $hour; ?></span>
            <?php } ?>
        </div>
    </div>
