<section class="bg-white py-6">
    <div class="container">
        <div class="row mb-5">
            <div class="col-11 mx-auto">
                <h2 class="text-bold display-4"><?php echo __('account.heading.password_reset'); ?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-11 mx-auto">
                <div class="row align-items-center">
                    <div class="col-12">
                        <?php echo $view->alert ?? null;?>
                        <?php echo $view->form; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
