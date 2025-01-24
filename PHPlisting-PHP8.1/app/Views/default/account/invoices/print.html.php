<!doctype html>
<html lang="<?php echo locale()->getLocale(); ?>" dir="<?php echo locale()->getDirection(); ?>">
    <head>
        <meta charset="utf-8">
        <style>
            body {
                font-family: DejaVu Sans;
                font-size: 14px;
            }

            .invoice-box {
                max-width: 800px;
                margin: auto;
                padding: 30px;
                border: 1px solid #eee;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
                font-size: 14px;
                line-height: 24px;
                font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
                color: #555;
            }

            .invoice-box table {
                width: 100%;
                line-height: inherit;
                text-align: left;
            }

            .invoice-box table td {
                padding: 5px;
                vertical-align: top;
            }

            .invoice-box table tr td:nth-child(2),
            .invoice-box table tr td:nth-child(4) {
                text-align: right;
            }

            .invoice-box table tr.top table td {
                padding-bottom: 20px;
            }

            .invoice-box table tr.top table td.title {
                font-size: 45px;
                line-height: 45px;
                color: #333;
            }

            .invoice-box table tr.information table td {
                padding-bottom: 40px;
            }

            .invoice-box table tr.heading td {
                background: #eee;
                border-bottom: 1px solid #ddd;
                font-weight: bold;
            }

            .invoice-box table tr.details td {
                padding-bottom: 20px;
            }

            .invoice-box table tr.item td {
                border-bottom: 1px solid #eee;
            }

            .invoice-box table tr.item.last td {
                border-bottom: none;
            }

            .invoice-box table tr.options td {
                border-top: 2px solid #eee;
                text-align: right;
            }

            .invoice-box table tr.total td {
                background: #eee;
                text-align: right;
            }

            @media only screen and (max-width: 600px) {
                .invoice-box table tr.top table td {
                    width: 100%;
                    display: block;
                    text-align: center;
                }

                .invoice-box table tr.information table td {
                    width: 100%;
                    display: block;
                    text-align: center;
                }
            }

            .invoice-box.rtl {
                direction: rtl;
                font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            }

            .invoice-box.rtl table {
                text-align: right;
            }

        </style>
    </head>
    <body>
        <div class="invoice-box <?php echo locale()->getDirection(); ?>">
            <table cellpadding="0" cellspacing="0">
                <tr class="information">
                    <td colspan="4">
                        <table>
                            <tr>
                                <td style="width: 50%;">
                                    <strong><?php echo __('invoice.label.from'); ?>:</strong> <br />
                                    <?php echo \nl2br(e(config()->billing->invoice_company_details)); ?>
                                    <br /><br />
                                    <strong><?php echo __('invoice.label.to'); ?>:</strong> <br />
                                    <?php echo e(auth()->user()->getName()); ?>
                                </td>
                                <td>
                                    <strong>
                                    <?php
                                        echo view('misc/status', [
                                            'type' => 'invoice',
                                            'status' => $view->invoice->status,
                                        ]);
                                    ?>
                                    </strong>
                                    <br /><br />
                                    <?php echo __('invoice.label.id'); ?>: <?php echo e($view->invoice->id); ?><br />
                                    <?php echo __('invoice.label.order_id'); ?>: <?php echo e($view->invoice->order_id); ?><br />
                                    <?php echo __('invoice.label.date'); ?>: <?php echo locale()->formatDatetime($view->invoice->added_datetime, auth()->user()->timezone); ?><br />
                                    <?php if ('paid' == $view->invoice->status) { ?>
                                        <?php echo __('invoice.label.paid_date'); ?>: <?php echo locale()->formatDatetime($view->invoice->paid_datetime, auth()->user()->timezone); ?><br />
                                    <?php } ?>
                                    <?php if ('cancelled' == $view->invoice->status) { ?>
                                        <?php echo __('invoice.label.cancelled_date'); ?>: <?php echo locale()->formatDatetime($view->invoice->cancelled_datetime, auth()->user()->timezone); ?><br />
                                    <?php } ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="heading">
                    <td><?php echo __('invoice.label.item'); ?></td>
                    <td><?php echo __('invoice.label.price'); ?></td>
                    <td><?php echo __('invoice.label.quantity'); ?></td>
                    <td><?php echo __('invoice.label.total'); ?></td>
                </tr>

                <tr class="item last">
                    <td><?php echo $view->invoice->getDescription(); ?></td>
                    <td><?php echo locale()->formatPrice($view->invoice->subtotal); ?></td>
                    <td>1</td>
                    <td><?php echo locale()->formatPrice($view->invoice->subtotal); ?></td>
                </tr>

                <tr class="options">
                    <td colspan="4">
                        <?php echo __('invoice.label.subtotal'); ?>: <?php echo locale()->formatPrice($view->invoice->subtotal); ?><br />

                        <?php if ($view->invoice->balance > 0) { ?>
                            <?php echo __('invoice.label.balance'); ?>: <?php echo locale()->formatPrice($view->invoice->balance); ?><br />
                        <?php } ?>

                        <?php if ($view->invoice->discount > 0) { ?>
                            <?php echo __('invoice.label.discount'); ?>: <?php echo locale()->formatPrice($view->invoice->discount); ?><br />
                        <?php } ?>

                        <?php if ($view->invoice->tax > 0) { ?>
                            <?php echo __('invoice.label.tax'); ?>: <?php echo locale()->formatPrice($view->invoice->tax); ?><br />
                        <?php } ?>

                        <?php if ($view->invoice->refund > 0) { ?>
                            <?php echo __('invoice.label.refund'); ?>: <?php echo locale()->formatPrice($view->invoice->refund); ?><br />
                        <?php } ?>
                    </td>
                </tr>
                <tr class="total">
                    <td colspan="4">
                        <strong><?php echo __('invoice.label.total'); ?>:</strong> <?php echo locale()->formatPrice($view->invoice->total); ?>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
