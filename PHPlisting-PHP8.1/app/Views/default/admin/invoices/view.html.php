<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('manage/' . $view->type->slug, session()->get('admin/manage/' . $view->type->slug)); ?>"><?php echo e(__('admin.invoices.breadcrumb.listings', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo adminRoute($view->type->slug . '-invoices', session()->get('admin/' . $view->type->slug . '-invoices')); ?>"><?php echo e(__('admin.invoices.breadcrumb.invoices', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a></li>
        <li class="breadcrumb-item active"><?php echo e(__('admin.invoices.breadcrumb.view', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'id' => $view->invoice->id])); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.invoices.heading.view', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural, 'id' => $view->invoice->id, 'listing' => e($view->invoice->order->listing->title)])); ?></h3>
</div>
<div class="mb-3">
    <a href="<?php echo adminRoute($view->type->slug . '-invoices/print/' . $view->invoice->id); ?>" target="_blank" class="btn btn-info btn-lg"><?php echo e(__('invoice.button.print')); ?></a>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-md border-0 rounded-0 p-3">
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
                                            <?php echo e($view->invoice->user->getName()); ?>
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
                <div class="col-12 my-5">
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
