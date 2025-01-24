<?php
    $statistics = \App\Models\Stat::query()
        ->select('count AS total, date')
        ->where('type', 'listing_impression')
        ->where('type_id', $view->listing->id)
        ->where(db()->raw('DATEDIFF(CURDATE(), DATE(date)) <= ?', [30]))
        ->orderBy('date')
        ->get([1]);

    $dates = [];

    $date = (new \DateTime())->setTime(0,0,0);
    $addedDate = (new \DateTime($view->listing->added_datetime))->setTime(0,0,0);
    $interval = new \DateInterval('P1D');

    for ($i = 0; $i < 31; $i++) {
        $date->sub($interval);

        if ($date < $addedDate) {
            break;
        }

        if (null !== $stat = $statistics->where('date', $date->format('Y-m-d'))->first()) {
            $dates[locale()->formatDate($date->format('Y-m-d'))] = $stat->total;
        } else {
            $dates[locale()->formatDate($date->format('Y-m-d'))] = 0;
        }
    }
        
    layout()->addFooterJs('<script src="' . asset('js/chartjs/Chart.min.js?v=391') . '"></script>');
    layout()->addFooterJs('<script>
    var ctx = document.getElementById(\'statistics\').getContext(\'2d\');
    var chart = new Chart(ctx, {
        type: \'line\',
        data: {
            labels: ["' . implode('", "', array_keys(array_reverse($dates))) . '"],
            datasets: [{
                borderColor: \'rgb(255, 99, 132)\',
                data: ["' . implode('", "', array_reverse($dates)) . '"],
                pointStyle: \'circle\',
                pointRadius: 5,
                pointHoverRadius: 10
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: \'' . __('listing.label.impressions') . '\'
                    },
                    ticks: {
                        stepSize: 1,
                    }
                }
            }
        }
    });
</script>');
 
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('manage/' . $view->type->slug, session()->get('admin/manage/' . $view->type->slug)); ?>"><?php echo e(__('admin.listings.breadcrumb.index', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('admin.listings.breadcrumb.summary', ['singular' => $view->type->name_singular, 'plural' => $view->type->name_plural])); ?></li>
    </ol>
</nav>
<div class="mb-4">
    <h3 class="mb-3"><?php echo e($view->listing->title); ?></h3>
    <?php if (null !== $view->listing->get('_page')) { ?>
        <a target="_blank" href="<?php echo route($view->type->slug . '/' . $view->listing->slug); ?>" class="btn btn-round mb-sm-2 btn-light" role="button"><?php echo __('admin.listings.button.preview'); ?></a>
    <?php } ?>
    <a href="<?php echo adminRoute('manage/' . $view->type->slug . '/update/' . $view->listing->id); ?>" class="btn btn-round mb-sm-2 btn-light" role="button"><?php echo __('admin.listings.button.edit'); ?></a>
    <?php if (false !== auth()->check('admin_users')) { ?>
        <a href="<?php echo adminRoute('users/summary/' . $view->listing->user_id); ?>" class="btn btn-round mb-sm-2 btn-light" role="button"><?php echo __('admin.listings.button.user'); ?></a>
    <?php } ?>
    <?php if (null !== $view->type->reviewable && null !== $view->listing->get('_reviews')) { ?>
        <?php if (false !== auth()->check(['admin_content', 'admin_reviews'])) { ?>
            <a href="<?php echo adminRoute($view->type->slug . '-reviews', ['listing_id' => $view->listing->id]); ?>" class="btn btn-round mb-sm-2 btn-light" role="button"><?php echo __('admin.listings.button.reviews', ['count' => '<span class="badge badge-secondary">' . $view->listing->reviews->count() . '</span>']); ?></a>
        <?php } ?>
    <?php } ?>
    <?php if (false !== auth()->check(['admin_content', 'admin_messages'])) { ?>
        <a href="<?php echo adminRoute($view->type->slug . '-messages', ['listing_id' => $view->listing->id]); ?>" class="btn btn-round mb-sm-2 btn-light" role="button"><?php echo __('admin.listings.button.messages', ['count' => '<span class="badge badge-secondary">' . $view->listing->messages->count() . '</span>']); ?></a>
    <?php } ?>
    <?php
        $invoices = $view->listing->order->invoices();

        if (false !== config()->compat->hide_free_pricing_invoices) {
            $invoices->where('subtotal', '>', 0);
        }
    ?>
    <a href="<?php echo adminRoute($view->type->slug . '-invoices', ['listing_id' => $view->listing->id]); ?>" class="btn btn-round mb-sm-2 btn-light" role="button"><?php echo __('admin.listings.button.invoices', ['count' => '<span class="badge badge-secondary">' . $invoices->count() . '</span>']); ?></a>
    <a href="<?php echo adminRoute($view->type->slug . '-claims', ['listing_id' => $view->listing->id]); ?>" class="btn btn-round mb-sm-2 btn-light" role="button"><?php echo __('admin.listings.button.claims', ['count' => '<span class="badge badge-secondary">' . $view->listing->claims->count() . '</span>']); ?></a>
    <a href="<?php echo adminRoute('manage/' . $view->type->slug . '/delete/' . $view->listing->id); ?>" data-toggle="confirmation" data-title="<?php echo e(__('default.confirmation.message')); ?>" data-btn-ok-label="<?php echo e(__('default.confirmation.yes')); ?>" data-btn-cancel-label="<?php echo e(__('default.confirmation.no')); ?>" class="btn btn-round mb-sm-2 btn-danger" role="button"><?php echo __('admin.listings.button.delete'); ?></a>
</div>
<div class="row">
    <div class="col-12 col-lg-4 col-md-6 mb-4">
        <div class="card h-100 shadow-md border-0 rounded-0">
            <div class="card-header d-flex align-items-center justify-content-between p-4 border-0">
                <h4 class="text-bold display-8"><?php echo __('admin.listings.summary.heading.details'); ?></h4>
                <?php
                    echo view('misc/status', [
                        'type' => 'listing',
                        'status' => $view->listing->active,
                    ]);
                ?>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th class="border-0" scope="row"><?php echo __('admin.listings.label.id'); ?></th>
                                <td class="border-0 text-right"><?php echo e($view->listing->id); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('admin.listings.label.type'); ?></th>
                                <td class="text-right"><?php echo e($view->listing->type->name_singular); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('admin.listings.label.category'); ?></th>
                                <td class="text-right"><?php echo e($view->listing->getOutputableValue('_category')); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('admin.listings.label.extra_categories'); ?></th>
                                <td class="text-right"><?php echo $view->listing->categories()->count(); ?> / <?php echo $view->listing->_extra_categories; ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('admin.listings.label.date'); ?></th>
                                <td class="text-right"><?php echo locale()->formatDatetime($view->listing->added_datetime, auth()->user()->timezone); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('admin.listings.label.updated_date'); ?></th>
                                <td class="text-right"><?php echo locale()->formatDatetimeDiff($view->listing->updated_datetime); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4 col-md-6 mb-4">
        <div class="card h-100 shadow-md border-0 rounded-0">
            <div class="card-header d-flex align-items-center justify-content-between p-4 border-0">
                <h4 class="text-bold display-8"><?php echo __('admin.listings.summary.heading.order'); ?></h4>
                <?php
                    echo view('misc/status', [
                        'type' => 'order',
                        'status' => $view->listing->status,
                    ]);
                ?>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th class="border-0" scope="row"><?php echo __('admin.listings.label.product'); ?></th>
                                <td class="border-0 text-right"><?php echo e($view->listing->order->pricing->product->name); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('admin.listings.label.pricing'); ?></th>
                                <td class="text-right"><?php echo e($view->listing->order->pricing->getName()); ?></td>
                            </tr>
                            <?php if (null !== $view->listing->discount_id && null !== $view->listing->discount) { ?>
                            <tr>
                                <th scope="row"><?php echo __('admin.listings.label.discount'); ?></th>
                                <td class="text-right"><?php echo e($view->listing->order->discount->code); ?></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <th scope="row"><?php echo __('admin.listings.label.next_due_date'); ?></th>
                                <td class="text-right"><?php echo locale()->formatDatetime($view->listing->order->end_datetime, auth()->user()->timezone); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4 col-md-6 mb-4">
        <div class="card h-100 shadow-md border-0 rounded-0">
            <div class="card-header d-flex align-items-center justify-content-between p-4 border-0">
                <h4 class="text-bold display-8"><?php echo __('admin.listings.summary.heading.statistics'); ?></h4>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th class="border-0" scope="row"><?php echo __('admin.listings.label.impressions'); ?></th>
                                <td class="border-0 text-right"><?php echo e($view->listing->impressions ?? 0); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('admin.listings.label.search_impressions'); ?></th>
                                <td class="text-right"><?php echo e($view->listing->search_impressions ?? 0); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('admin.listings.label.phone_views'); ?></th>
                                <td class="text-right"><?php echo e($view->listing->phone_views ?? 0); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo __('admin.listings.label.website_clicks'); ?></th>
                                <td class="text-right"><?php echo e($view->listing->website_clicks ?? 0); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mb-4">
        <div class="card shadow-md border-0 rounded-0">
            <div class="card-body p-4">
                <?php echo $view->alert ?? null; ?>
                <?php echo $view->form->render('form/vertical'); ?>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card shadow-md border-0 rounded-0">
            <div class="card-header p-4 border-0">
                <h4 class="text-bold display-8"><?php echo __('admin.listings.summary.heading.chart'); ?></h4>
            </div>
            <div class="card-body p-4">
                <canvas id="statistics"></canvas>
            </div>
        </div>
    </div>
</div>
