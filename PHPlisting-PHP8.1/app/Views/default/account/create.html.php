<section class="bg-white py-6">
    <div class="container">
        <div class="row mb-4">
            <div class="col-11 mx-auto">
                <h2 class="text-bold display-4"><?php echo __('account.heading.create'); ?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-11 mx-auto">
                <div class="row align-items-center">
                    <div class="col-12">
                        <p class="pb-2"><?php echo __('account.label.already_have_an_account', ['url' => route('account/login')]); ?></p>
                        <?php echo $view->alert ?? null;?>
                        <?php echo $view->form; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
