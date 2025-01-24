<section class="widget contact-form-widget <?php echo $view->settings->colorscheme; ?> py-6">
    <div class="container">
        <div class="row">
            <div class="col-12 mb-5">
                <p class="text-caption text-black text-uppercase display-11 l-space-1"><?php echo e($view->settings->heading); ?></p>
                <h3 class="text-black display-5 l-space-0"><?php echo e($view->settings->description); ?></h3>
            </div>
            <div class="col-12">
                <?php echo session('success_contact'); ?>
                <?php echo $view->alert ?? null;?>
                <?php echo $view->form; ?>
            </div>
        </div>
    </div>
</section>
