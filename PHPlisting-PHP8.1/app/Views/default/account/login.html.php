<section class="bg-white py-6">
    <div class="container">
        <div class="row mb-4">
            <div class="col-11 mx-auto">
                <h2 class="text-bold display-4"><?php echo __('account.heading.login'); ?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-11 mx-auto">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-8 bd-box-right pr-md-4">
                        <p class="pb-2"><?php echo __('account.label.dont_have_an_account', ['url' => route('account/create')]); ?></p>
                        <?php echo $view->alert ?? null;?>
                        <?php echo session('success'); ?>
                        <?php echo session('error'); ?>
                        <?php echo $view->form; ?>
                    </div>
                    <div class="col-12 col-lg-4 pl-md-4 mt-4 mt-lg-0">
<!--
                       <?php if ('' != config()->account->get('facebook_app_id', '')) { ?>
                           <a href="<?php echo route('account/login/facebook'); ?>" class="btn btn-block btn-social btn-lg btn-facebook mb-3">
                                <i class="fab fa-facebook-f"></i>
                                <span class="display-9">Sign in with Facebook</span>
                           </a>
                       <?php } ?>
-->
                       <?php if ('' != config()->account->get('google_client_id', '')) { ?>
                           <a href="<?php echo route('account/login/google'); ?>" class="btn btn-block btn-social btn-lg btn-google mb-3">
                                <i class="fab fa-google"></i>
                                <span class="display-9">Sign in with Google</span>
                           </a>
                       <?php } ?>
                       <?php if ('' != config()->account->get('twitter_client_id', '')) { ?>
                           <a href="<?php echo route('account/login/twitter'); ?>" class="btn btn-block btn-social btn-lg btn-twitter mb-3">
                                <i class="fab fa-twitter"></i>
                                <span class="display-9">Sign in with Twitter</span>
                           </a>
                       <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
