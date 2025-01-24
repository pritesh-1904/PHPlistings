<div class="container">
    <div class="row py-6">
        <?php echo view('account/menu'); ?>
        <div class="col-12 col-lg-9 bd-box pl-lg-4">
            <div class="row">
                <div class="col-12 mb-5">
                    <h2 class="text-bold display-4"><?php echo __('message.heading'); ?></h2>
                </div>
                <div class="col-12">
                    <?php echo session('success') ?? null; ?>
                    <?php echo session('error') ?? null; ?>
                </div>
                <div class="col-12">
                    <div class="card shadow-md border-0">
                        <div class="card-body">
                            <?php echo $view->messages; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
