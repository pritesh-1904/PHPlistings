<div class="container">
    <div class="row py-6">
        <?php echo view('account/menu'); ?>
        <div class="col-12 col-lg-9 bd-box pl-lg-4">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb d-flex">
                            <li class="breadcrumb-item"><a href="<?php echo route('account/invoices', session()->get('account/invoices')); ?>"><?php echo __('invoice.breadcrumb.results'); ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo __('invoice.breadcrumb.active', ['id' => $view->invoice->id]); ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-12 mb-5">
                    <h2 class="text-bold display-4"><?php echo __('invoice.heading.active', ['id' => $view->invoice->id]); ?></h2>
                </div>
                <div class="col-12 mb-3">
                    <?php if ('pending' == $view->invoice->status) { ?>
                        <a href="<?php echo route('account/checkout/' . $view->invoice->id); ?>" class="btn btn-success btn-lg"><?php echo e(__('invoice.button.pay')); ?></a>
                    <?php } ?>
                    <a href="<?php echo route('account/invoices/print/' . $view->invoice->id); ?>" target="_blank" class="btn btn-info btn-lg"><?php echo e(__('invoice.button.print')); ?></a>
                </div>
                <div class="col-12 mb-5">
                    <div class="card border-0 shadow-md px-2 p-3">
                        <div class="card-body">
                            <div class="row justify-content-between mb-5">
                                <div class="col">
                                    <div class="table-responsive mb-4 mb-md-0">
                                        <table class="table table-sm table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th scope="row"><?php echo __('invoice.label.from'); ?></th>
                                                    <td>
                                                        <p class="m-0"><?php echo \nl2br(e(config()->billing->invoice_company_details)); ?></p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?php echo __('invoice.label.to'); ?></th>
                                                    <td>
                                                        <?php echo e(auth()->user()->getName()); ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-sm-auto ml-auto">
                                     <div class="table-responsive">
                                         <table class="table table-borderless">
                                             <tbody>
                                                 <tr>
                                                     <th scope="row">
                                                         <?php echo __('invoice.label.id'); ?></th>
                                                     <td>
                                                         <?php echo e($view->invoice->id); ?>
                                                     </td>
                                                 </tr>
                                                 <tr>
                                                     <th scope="row">
                                                         <?php echo __('invoice.label.order_id'); ?></th>
                                                     <td>
                                                         <?php echo e($view->invoice->order_id); ?>
                                                     </td>
                                                 </tr>
                                                 <tr>
                                                     <th scope="row"><?php echo __('invoice.label.status'); ?></th>
                                                     <td>
                                                        <?php
                                                            echo view('misc/status', [
                                                                'type' => 'invoice',
                                                                'status' => $view->invoice->status,
                                                            ]);
                                                        ?>
                                                     </td>
                                                 </tr>
                                                 <tr>
                                                     <th scope="row"><?php echo __('invoice.label.date'); ?></th>
                                                     <td>
                                                         <?php echo locale()->formatDatetime($view->invoice->added_datetime, auth()->user()->timezone); ?>
                                                     </td>
                                                 </tr>
                                                 <?php if ('paid' == $view->invoice->status) { ?>
                                                 <tr>
                                                     <th scope="row"><?php echo __('invoice.label.paid_date'); ?></th>
                                                     <td>
                                                         <?php echo locale()->formatDatetime($view->invoice->paid_datetime, auth()->user()->timezone); ?>
                                                     </td>
                                                 </tr>
                                                 <?php } ?>
                                                 <?php if ('cancelled' == $view->invoice->status) { ?>
                                                 <tr>
                                                     <th scope="row"><?php echo __('invoice.label.cancelled_date'); ?></th>
                                                     <td>
                                                         <?php echo locale()->formatDatetime($view->invoice->cancelled_datetime, auth()->user()->timezone); ?>
                                                     </td>
                                                 </tr>
                                                 <?php } ?>
                                             </tbody>
                                         </table>
                                     </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table">
                                           <thead>
                                               <tr>
                                                   <th scope="col"><?php echo __('invoice.label.item'); ?></th>
                                                   <th scope="col"><?php echo __('invoice.label.price'); ?></th>
                                                   <th scope="col"><?php echo __('invoice.label.quantity'); ?></th>
                                                   <th scope="col"><?php echo __('invoice.label.total'); ?></th>
                                               </tr>
                                           </thead>
                                           <tbody>
                                               <tr>
                                                   <td class="border-top-0 border-bottom"><?php echo $view->invoice->getDescription(); ?></td>
                                                   <td class="border-top-0 border-bottom"><?php echo locale()->formatPrice($view->invoice->subtotal); ?></td>
                                                   <td class="border-top-0 border-bottom">1</td>
                                                   <td class="border-top-0 border-bottom"><?php echo locale()->formatPrice($view->invoice->subtotal); ?></td>
                                               </tr>
                                               <tr>
                                                   <th class="text-right border-bottom" scope="row" colspan="3"><?php echo __('invoice.label.subtotal'); ?></th>
                                                   <td class="border-top-0 border-bottom"><?php echo locale()->formatPrice($view->invoice->subtotal); ?></td>
                                               </tr>
                                               <?php if ($view->invoice->balance > 0) { ?>
                                               <tr>
                                                   <th class="text-right border-bottom"  scope="row" colspan="3"><?php echo __('invoice.label.balance'); ?></th>
                                                   <td class="border-top-0 border-bottom"><?php echo locale()->formatPrice($view->invoice->balance); ?></td>
                                               </tr>
                                               <?php } ?>
                                               <?php if ($view->invoice->discount > 0) { ?>
                                               <tr>
                                                   <th class="text-right border-bottom"  scope="row" colspan="3"><?php echo __('invoice.label.discount'); ?></th>
                                                   <td class="border-top-0 border-bottom"><?php echo locale()->formatPrice($view->invoice->discount); ?></td>
                                               </tr>
                                               <?php } ?>
                                               <?php if ($view->invoice->tax > 0) { ?>
                                               <tr>
                                                   <th class="text-right border-bottom"  scope="row" colspan="3"><?php echo __('invoice.label.tax'); ?></th>
                                                   <td class="border-top-0 border-bottom"><?php echo locale()->formatPrice($view->invoice->tax); ?></td>
                                               </tr>
                                               <?php } ?>
                                               <?php if ($view->invoice->refund > 0) { ?>
                                               <tr class="table-danger">
                                                   <th class="text-right border-bottom" scope="row" colspan="3"><?php echo __('invoice.label.refund'); ?></th>
                                                   <td class="border-top-0 border-bottom"><?php echo locale()->formatPrice($view->invoice->refund); ?></td>
                                               </tr>
                                               <?php } ?>
                                               <tr class="table-secondary">
                                                   <th class="text-right border-bottom" scope="row" colspan="3"><?php echo __('invoice.label.total'); ?></th>
                                                   <td class="border-top-0 border-bottom"><?php echo locale()->formatPrice($view->invoice->total); ?></td>
                                               </tr>
                                           </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-5">
                    <h2 class="text-bold display-4"><?php echo __('transaction.heading'); ?></h2>
                </div>
                <div class="col-12">
                    <div class="card shadow-md border-0">
                        <div class="card-body">
                            <?php echo $view->transactions; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
