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
                <div class="col-12">
                    <div class="card border-0 shadow-md px-2 p-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div>
                                        <?php echo session()->get('success'); ?>
                                        <?php echo session()->get('error'); ?>
                                        <?php echo $view->alert ?? null; ?>
                                    </div>
                                    <div class="table-responsive mb-5">
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
                                               <tr class="table-secondary">
                                                   <th class="text-right border-bottom" scope="row" colspan="3"><?php echo __('invoice.label.total'); ?></th>
                                                   <td class="border-top-0 border-bottom"><?php echo locale()->formatPrice($view->invoice->total); ?></td>
                                               </tr>
                                           </tbody>
                                        </table>
                                    </div>
                                    <div>
                                        <?php echo $view->form; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
