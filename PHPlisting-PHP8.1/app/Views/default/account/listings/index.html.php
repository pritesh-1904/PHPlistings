<div class="container">
    <div class="row py-6">
        <?php echo view('account/menu'); ?>
        <div class="col-12 col-lg-9 bd-box pl-lg-4">
            <div class="row">
                <div class="col-12 mb-5">
                    <h2 class="text-bold display-4"><?php echo __('listing.heading', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural]); ?></h2>
                </div>
                <div class="col-12 mb-3">
                    <a href="<?php echo route('account/manage/' . $view->type->slug . '/create'); ?>" class="btn btn-success btn-lg"><i class="fas fa-plus"></i> <?php echo e(__('listing.button.create', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a>
                    <div class="float-right">
                        <?php echo $view->form; ?>
                    </div>
                </div>
                <div class="col-12">
                    <?php echo session('success') ?? null; ?>
                    <?php echo session('error') ?? null; ?>
                </div>
                <div class="col-12">
                    <div class="card shadow-md border-0">
                        <div class="card-body">
                            <?php echo $view->listings; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
