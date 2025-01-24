<div class="container">
    <div class="row py-6">
        <?php echo view('account/menu'); ?>
        <div class="col-12 col-lg-9 bd-box pl-lg-4">
            <div class="row">
                <div class="col-12 mb-5">
                    <h2 class="text-bold display-4"><?php echo __('account.heading.profile'); ?></h2>
                </div>
                <div class="col-12">
                    <div class="card shadow-md border-0 p-3">
                        <div class="card-body">
                            <?php echo $view->alert ?? null; ?>
                            <?php echo $view->form; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

