<strong><?php echo config()->billing->invoice_product_name; ?></strong>, <?php echo $view->invoice->order->listing->title; ?> (id: <?php echo $view->invoice->order->listing->id; ?>)
<?php if (null !== $view->invoice->pricing) { ?>
<br /><strong><?php echo e(__('invoice.label.product')); ?>:</strong> <?php echo $view->invoice->pricing->getNameWithProduct(); ?>
<?php } ?>
<?php if (null !== $view->invoice->start_datetime) { ?>
    <br /><strong><?php echo e(__('invoice.label.billing_cycle')); ?>:</strong> <?php echo locale()->formatDatetime($view->invoice->start_datetime, auth()->user()->timezone) . ' - ' . locale()->formatDatetime($view->invoice->end_datetime, auth()->user()->timezone); ?>
<?php } ?>
