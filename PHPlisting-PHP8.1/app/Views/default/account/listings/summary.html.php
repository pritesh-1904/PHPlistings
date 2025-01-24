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
<div class="container">
    <div class="row py-6">
        <?php echo view('account/menu'); ?>
        <div class="col-12 col-lg-9 bd-box pl-lg-4">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb d-flex">
                            <li class="breadcrumb-item"><a href="<?php echo route('account/manage/' . $view->listing->type->slug, session()->get('account/manage/' . $view->listing->type->slug)); ?>"><?php echo __('listing.breadcrumb.results', ['singular' => $view->listing->type->name_singular, 'plural' => $view->listing->type->name_plural]); ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo __('listing.breadcrumb.active', ['listing' => e($view->listing->title)]); ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-12 mb-4">
                    <h2 class="text-bold display-4 mb-4"><?php echo e($view->listing->title); ?></h2>
                    <p>
                        <?php if (null !== $view->listing->get('_page')) { ?>
                            <a target="_blank" href="<?php echo route($view->type->slug . '/' . $view->listing->slug); ?>" class="btn btn-round mb-sm-2 btn-light" role="button"><?php echo __('listing.button.preview'); ?></a>
                        <?php } ?>
                        <a href="<?php echo route('account/manage/' . $view->type->slug . '/update/' . $view->listing->slug); ?>" class="btn btn-round mb-sm-2 btn-light" role="button"><?php echo __('listing.button.edit'); ?></a>
                        <?php if (null !== $view->type->reviewable && null !== $view->listing->get('_reviews')) { ?>
                            <a href="<?php echo route('account/manage/' . $view->type->slug . '/reviews/' . $view->listing->slug); ?>" class="btn btn-round mb-sm-2 btn-light" role="button"><?php echo __('listing.button.reviews'); ?></a>
                        <?php } ?>
                        <a href="<?php echo route('account/invoices', ['listing_id' => $view->listing->id]); ?>" class="btn btn-round mb-sm-2 btn-light" role="button"><?php echo __('listing.button.invoices'); ?></a>
                        <?php if (null !== $view->listing->claimed) { ?>
                            <a href="<?php echo route('account/claims', ['listing_id' => $view->listing->id]); ?>" class="btn btn-round mb-sm-2 btn-light" role="button"><?php echo __('listing.button.claims'); ?></a>
                        <?php } ?>
                    </p> 
                    <?php echo $view->info ?? null; ?>
                </div>
                <div class="col-12 mb-5">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-5">
                            <div class="card h-100 shadow-md border-0">
                                <div class="card-header d-flex align-items-center justify-content-between p-4 border-0">
                                    <h4 class="text-bold display-8"><?php echo __('listing.summary.heading.details'); ?></h4>
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
                                                    <th class="border-0" scope="row"><?php echo __('listing.label.id'); ?></th>
                                                    <td class="border-0 text-right"><?php echo e($view->listing->id); ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?php echo __('listing.label.type'); ?></th>
                                                    <td class="text-right"><?php echo e($view->listing->type->name_singular); ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?php echo __('listing.label.category'); ?></th>
                                                    <td class="text-right"><?php echo e($view->listing->getOutputableValue('_category')); ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?php echo __('listing.label.extra_categories'); ?></th>
                                                    <td class="text-right"><?php echo $view->listing->categories()->count(); ?> / <?php echo $view->listing->_extra_categories; ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?php echo __('listing.label.date'); ?></th>
                                                    <td class="text-right"><?php echo locale()->formatDatetime($view->listing->added_datetime, auth()->user()->timezone); ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?php echo __('listing.label.updated_date'); ?></th>
                                                    <td class="text-right"><?php echo locale()->formatDatetimeDiff($view->listing->updated_datetime); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-5">
                            <div class="card h-100 shadow-md border-0">
                                <div class="card-header d-flex align-items-center justify-content-between p-4 border-0">
                                    <h4 class="text-bold display-8"><?php echo __('listing.summary.heading.order'); ?></h4>
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
                                                    <th class="border-0" scope="row"><?php echo __('listing.label.product'); ?></th>
                                                    <td class="border-0 text-right"><?php echo e($view->listing->order->pricing->product->name); ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?php echo __('listing.label.pricing'); ?></th>
                                                    <td class="text-right"><?php echo e($view->listing->order->pricing->getName()); ?></td>
                                                </tr>
                                                <?php if (null !== $view->listing->discount_id && null !== $view->listing->order->discount) { ?>
                                                <tr>
                                                    <th scope="row"><?php echo __('listing.label.discount'); ?></th>
                                                    <td class="text-right"><?php echo e($view->listing->order->discount->code); ?></td>
                                                </tr>
                                                <?php } ?>
                                                <tr>
                                                    <th scope="row"><?php echo __('listing.label.next_due_date'); ?></th>
                                                    <td class="text-right"><?php echo locale()->formatDatetime($view->listing->order->end_datetime, auth()->user()->timezone); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card h-100 shadow-md border-0">
                                <div class="card-header d-flex align-items-center justify-content-between p-4 border-0">
                                    <h4 class="text-bold display-8"><?php echo __('listing.summary.heading.statistics'); ?></h4>
                                </div>
                                <div class="card-body p-3">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th class="border-0" scope="row"><?php echo __('listing.label.impressions'); ?></th>
                                                    <td class="border-0 text-right"><?php echo e($view->listing->impressions ?? 0); ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?php echo __('listing.label.search_impressions'); ?></th>
                                                    <td class="text-right"><?php echo e($view->listing->search_impressions ?? 0); ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?php echo __('listing.label.phone_views'); ?></th>
                                                    <td class="text-right"><?php echo e($view->listing->phone_views ?? 0); ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?php echo __('listing.label.website_clicks'); ?></th>
                                                    <td class="text-right"><?php echo e($view->listing->website_clicks ?? 0); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card h-100 shadow-md border-0">
                                <div class="card-header d-flex align-items-center justify-content-between p-4 border-0">
                                    <h4 class="text-bold display-8"><?php echo __('listing.summary.heading.qr'); ?></h4>
                                </div>
                                <div class="card-body p-3">
                                    <?php 
                                        $attributes = attr([
                                            'src' => qrcode(route($view->listing->type->slug . '/' . $view->listing->slug), 10, 6),
                                            'class' => 'img-fluid w-100',
                                        ]);
                                    ?>
                                    <img <?php echo $attributes; ?> />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-4">
                    <div class="card shadow-md border-0">
                        <div class="card-header p-4 border-0">
                            <h4 class="text-bold display-8"><?php echo __('listing.summary.heading.chart'); ?></h4>
                        </div>
                        <div class="card-body p-4">
                            <canvas id="statistics"></canvas>
                        </div>
                    </div>
                </div>
                <?php if ($view->form->getFields()->count() > 0) { ?>
                <div class="col-12">
                    <div class="card border-0 shadow-md">
                        <div class="card-body p-4">
                            <?php echo $view->alert ?? null; ?>
                            <?php echo $view->form->render('form/vertical'); ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
