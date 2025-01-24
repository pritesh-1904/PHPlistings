<div class="container">
    <div class="row py-6">
        <?php echo view('account/menu'); ?>
        <div class="col-12 col-lg-9 bd-box pl-lg-4">
            <div class="row">
                <?php if (null !== $view->listing) { ?>
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb pl-0 bg-transparent">
                            <li class="breadcrumb-item"><a href="<?php echo route('account/manage/' . $view->listing->type->slug . '/summary/' . $view->listing->slug); ?>"><?php echo e(__('claim.breadcrumb.listing', ['listing' => $view->listing->title])); ?></a></li>
                            <li class="breadcrumb-item active"><?php echo e(__('claim.breadcrumb.index')); ?></li>
                        </ol>
                    </nav>
                </div>
                <?php } ?>
                <div class="col-12 mb-3">
                    <h2 class="text-bold display-4"><?php echo __('claim.heading'); ?></h2>
                </div>
                <div class="col-12 mb-3">
                    <?php echo $view->form; ?>
                </div>
                <div class="col-12 mb-5">            
                    <div class="card shadow-md border-0">
                        <div class="card-body">
                            <?php echo $view->claims; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
