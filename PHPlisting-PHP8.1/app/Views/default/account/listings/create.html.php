<div class="container">
    <div class="row py-6">
        <?php echo view('account/menu'); ?>
        <div class="col-12 col-lg-9 bd-box pl-lg-4">
            <div class="row">
                <div class="col-12 mb-5">
                    <h2 class="text-bold display-4"><?php echo __('listing.heading.create', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural]); ?></h2>
                </div>
                <div class="col-12">
                    <div class="card shadow-md border-0 p-3">
                        <div class="card-body">
                            <?php echo session('error') ?? null; ?>
                            <?php echo $view->info ?? null; ?>
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
