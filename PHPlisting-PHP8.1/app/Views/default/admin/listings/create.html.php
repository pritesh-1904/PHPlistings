<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('manage/' . $view->type->slug, session()->get('admin/manage/' . $view->type->slug)); ?>"><?php echo e(__('admin.listings.breadcrumb.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo __('admin.listings.breadcrumb.create', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural]); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.listings.heading.create', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></h3>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-md border-0 rounded-0 p-3">
            <div class="card-body">
                <?php echo $view->alert ?? null; ?>
                <?php echo $view->form; ?>
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
