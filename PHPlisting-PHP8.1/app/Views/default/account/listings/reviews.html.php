<div class="container">
    <div class="row py-6">
        <?php echo view('account/menu'); ?>
        <div class="col-12 col-lg-9 bd-box pl-lg-4">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb d-flex">
                            <li class="breadcrumb-item"><a href="<?php echo route('account/manage/' . $view->listing->type->slug); ?>"><?php echo __('listing.breadcrumb.results', ['singular' => $view->listing->type->name_singular, 'plural' => $view->listing->type->name_plural]); ?></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo route('account/manage/' . $view->listing->type->slug . '/summary/' . $view->listing->slug); ?>"><?php echo __('listing.breadcrumb.summary', ['listing' => e($view->listing->title)]); ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo __('review.breadcrumb.results'); ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-12 mb-5">
                    <h2 class="text-bold display-4"><?php echo __('review.heading'); ?></h2>
                </div>
                <div class="col-12">
                    <?php echo session('success') ?? null; ?>
                    <?php echo session('error') ?? null; ?>
                </div>
                <div class="col-12">
                    <div class="card shadow-md border-0 p-3">
                        <div class="card-body">
                            <?php echo $view->reviews; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
