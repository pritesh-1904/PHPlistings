<div class="container">
    <div class="row py-6">
        <?php echo view('account/menu'); ?>
        <div class="col-12 col-lg-9 bd-box pl-lg-4">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb d-flex">
                            <li class="breadcrumb-item"><a href="<?php echo route('account/manage/' . $view->listing->type->slug, session()->get('account/manage/' . $view->listing->type->slug)); ?>"><?php echo __('listing.breadcrumb.results', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural]); ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo __('listing.breadcrumb.active', ['listing' => e($view->listing->title)]); ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-12 mb-5">
                    <h2 class="text-bold display-4"><?php echo __('listing.heading.update', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural]); ?></h2>
                </div>
                <div class="col-12">      
                    <div class="card shadow-md border-0 p-3">
                        <div class="card-body">
                            <?php echo $view->alert ?? null; ?>
                            <?php echo $view->form->render('form/vertical'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ('Event' == $view->type->type) { ?>
    <script>
        $(document).ready(function() {
            show($('select[name="event_frequency"]').val());
            
            $('select[name="event_frequency"]').on("change", function() {
                show($(this).val());
            });

            function show(val)
            {
                $('input[name="event_interval"]').closest('div[class*="form-group"]').hide();
                $('input[name^="event_weekdays"]').closest('div[class*="form-group"]').hide();
                $('input[name^="event_weeks"]').closest('div[class*="form-group"]').hide();
                $('input[name="event_dates"]').closest('div[class*="form-group"]').hide();

                switch (val) {
                    case "once":
                        break;
                    case "yearly":
                    case "daily":
                        $('input[name="event_interval"]').closest('div[class*="form-group"]').show();
                        break;
                    case "weekly":
                        $('input[name="event_interval"]').closest('div[class*="form-group"]').show();
                        $('input[name^="event_weekdays"]').closest('div[class*="form-group"]').show();
                        break;
                    case "monthly":
                        $('input[name="event_interval"]').closest('div[class*="form-group"]').show();
                        $('input[name^="event_weekdays"]').closest('div[class*="form-group"]').show();
                        $('input[name^="event_weeks"]').closest('div[class*="form-group"]').show();
                        break;
                    case "custom":
                        $('input[name="event_dates"]').closest('div[class*="form-group"]').show();
                        break;
                }
            }
        });
    </script>
<?php } ?>
