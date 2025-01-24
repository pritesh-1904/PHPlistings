<div class="container">
    <div class="row py-6">
        <?php echo view('account/menu'); ?>
        <div class="col-12 col-lg-9 bd-box pl-lg-4">
            <div class="row">
                <div class="col-12 mb-5">
                    <?php echo session('success'); ?>
                    <h2 class="text-bold display-4"><?php echo __('account.dashboard.heading', ['first_name' => auth()->user()->first_name, 'last_name' => auth()->user()->last_name]); ?></h2>
                </div>
                <div class="col-12 mb-5">
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-4 mb-4">
                            <div class="card border-0 shadow-md py-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="card-text">
                                            <h5 class="display-5 text-medium"><?php echo auth()->user()->listings()->whereNull('active')->count(); ?></h5>
                                            <p class="text-secondary m-0"><?php echo __('account.dashboard.label.pending_listings'); ?></p>
                                        </div>
                                        <div class="card-icon">
                                            <i class="far fa-file-alt text-primary display-3"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 mb-4">
                            <div class="card border-0 shadow-md py-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="card-text">
                                            <h5 class="display-5 text-medium"><?php echo auth()->user()->claims()->where('status', 'pending')->count(); ?></h5>
                                            <p class="text-secondary m-0"><?php echo __('account.dashboard.label.pending_claims'); ?></p>
                                        </div>
                                        <div class="card-icon">
                                            <i class="far fa-clock text-primary display-3"></i>
                                        </div>
                                    </div>
                                    <a href="<?php echo route('account/claims', ['status' => 'pending']); ?>" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 mb-4">
                            <div class="card border-0 shadow-md py-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="card-text">
                                            <h5 class="display-5 text-medium"><?php echo auth()->user()->invoices()->where('status', 'pending')->count(); ?></h5>
                                            <p class="text-secondary m-0"><?php echo __('account.dashboard.label.unpaid_invoices'); ?></p>
                                        </div>
                                        <div class="card-icon">
                                            <i class="far fa-credit-card text-primary display-3"></i>
                                        </div>
                                    </div>
                                    <a href="<?php echo route('account/invoices', ['status' => 'pending']); ?>" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-5">
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-5">
                            <div class="card h-100 shadow-md border-0">
                                <div class="card-header p-4 border-0">
                                    <h4 class="text-bold display-8"><?php echo __('account.dashboard.label.latest_bookmarks'); ?></h4>
                                </div>
                                <div class="card-body p-3">
                                    <?php $bookmarks = auth()->user()->bookmarks()->withPivot(['id'])->orderBy('pivot_id', 'desc')->limit(5)->with('type')->get(); ?>
                                    <?php if ($bookmarks->count() > 0) { ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tbody>
                                                <?php foreach ($bookmarks as $bookmark) { ?>
                                                <tr>
                                                    <td class="border-top-0 border-bottom"><a href="<?php echo route($bookmark->type->slug . '/' . $bookmark->slug); ?>"><?php echo $bookmark->getOutputableValue('_title'); ?></a></td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php } else { ?>
                                    <div class="text-center py-5">
                                        <?php echo __('account.dashboard.label.no_bookmarks'); ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-5">
                            <div class="card h-100 shadow-md border-0">
                                <div class="card-header p-4 border-0">
                                    <h4 class="text-bold display-8"><?php echo __('account.dashboard.label.latest_messages'); ?></h4>
                                </div>
                                <div class="card-body p-3">
                                    <?php $messages = \App\Models\Message::query()
                                            ->where(function ($query) {
                                                return $query
                                                    ->where(function ($query) {
                                                        $query
                                                            ->where('active', 1)
                                                            ->where('recipient_id', auth()->user()->id);
                                                    })
                                                    ->orWhere('sender_id', auth()->user()->id);
                                            })
                                            ->with([
                                                'recipient',
                                                'sender',
                                            ])
                                            ->with('replies', function ($query) {
                                                return $query
                                                    ->where('user_id', '!=', auth()->user()->id)
                                                    ->whereNull('read_datetime');
                                            })
                                            ->orderBy('added_datetime', 'desc')
                                            ->limit(5)
                                            ->get();                                    
                                    ?>
                                    <?php if ($messages->count() > 0) { ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tbody>
                                                <?php foreach ($messages as $message) { ?>
                                                <tr>
                                                    <td class="border-top-0 border-bottom"><?php 
                                                        if (($message->recipient->id == auth()->user()->id && null === $message->read_datetime) || $message->replies->count() > 0) {
                                                            echo '<a href="' . route('account/messages/' . $message->id) . '"><strong>' . $message->title . '</strong></a>';
                                                        } else {
                                                            echo '<a href="' . route('account/messages/' . $message->id) . '">' . $message->title . '</a>';
                                                        }
                                                        ?>
                                                        <br />
                                                        <span class="text-secondary display-12"><?php echo ' ' . locale()->formatDatetimeDiff($message->added_datetime); ?></span>
                                                     </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php } else { ?>
                                    <div class="text-center py-5">
                                        <?php echo __('account.dashboard.label.no_messages'); ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
