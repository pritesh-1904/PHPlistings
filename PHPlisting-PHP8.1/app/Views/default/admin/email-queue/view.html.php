<nav aria-label="breadcrumb">
    <ol class="breadcrumb pl-0 bg-transparent">
        <li class="breadcrumb-item"><a href="<?php echo adminRoute('email-queue', session()->get('admin/email-queue')); ?>"><?php echo e(__('admin.emailqueue.breadcrumb.index')); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo e($view->email->getSubject()); ?></li>
    </ol>
</nav>
<div class="mb-5">
    <h3><?php echo e(__('admin.emailqueue.heading.view')); ?></h3>
</div>
<?php if (null !== $error = $view->email->getError()) echo view('flash/error', ['message' => $error]); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0 shadow-md p-3">
            <div class="card-body">
                <strong><?php echo e(__('admin.emailqueue.label.status')); ?>:</strong> 
                <?php echo view('misc/status', [
                        'type' => 'email',
                        'status' => $view->email->status,
                    ]);
                ?><br />
                <strong><?php echo e(__('admin.emailqueue.label.from')); ?>:</strong> <?php echo $view->email->extractName($view->email->getFrom()); ?> &lt;<?php echo $view->email->extractEmail($view->email->getFrom()); ?>&gt;<br />
                <strong><?php echo e(__('admin.emailqueue.label.to')); ?>:</strong> <?php echo $view->email->extractName($view->email->getTo()); ?> &lt;<?php echo $view->email->extractEmail($view->email->getTo()); ?>&gt;<br />
                <strong><?php echo e(__('admin.emailqueue.label.added_datetime')); ?>:</strong> <?php echo locale()->formatDatetime($view->email->added_datetime, auth()->user()->timezone); ?><br />
                <?php if (null !== $view->email->processed_datetime) { ?>
                    <strong><?php echo e(__('admin.emailqueue.label.processed_datetime')); ?>:</strong> <?php echo locale()->formatDatetime($view->email->processed_datetime, auth()->user()->timezone); ?><br />
                <?php } ?>
                <strong><?php echo e(__('admin.emailqueue.label.subject')); ?>:</strong> <?php echo $view->email->getSubject(); ?><br />
                <hr class="my-5">
                <p><?php echo d($view->email->getBody()); ?></p>
            </div>
        </div>
    </div>
</div>
