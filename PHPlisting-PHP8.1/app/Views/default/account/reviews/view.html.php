<div class="container">
    <div class="row py-6">
        <?php echo view('account/menu'); ?>
        <div class="col-12 col-lg-9 bd-box pl-lg-4">
            <div class="row">
                <?php if ($view->review->listing->user_id == auth()->user()->id) { ?>
                <div class="col-12 mb-2">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb d-flex">
                            <li class="breadcrumb-item"><a href="<?php echo route('account/manage/' . $view->review->listing->type->slug); ?>"><?php echo __('listing.breadcrumb.results', ['singular' => $view->review->listing->type->name_singular, 'plural' => $view->review->listing->type->name_plural]); ?></a></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo route('account/manage/' . $view->review->listing->type->slug . '/summary/' . $view->review->listing->slug); ?>"><?php echo __('listing.breadcrumb.summary', ['listing' => e($view->review->listing->title)]); ?></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo route('account/manage/' . $view->review->listing->type->slug . '/reviews/' . $view->review->listing->slug); ?>"><?php echo __('review.breadcrumb.index', ['listing' => e($view->review->listing->title)]); ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo __('review.breadcrumb.active', ['review' => e($view->review->title)]); ?></li>
                        </ol>
                    </nav>
                </div>
                <?php } ?>
                <div class="col-12 mb-5">            
                    <?php echo $view->alert ?? null; ?>
                    <div class="mb-3">
                        <span class="text-dark display-11"><?php echo __('review.details', [
                            'by' => e($view->review->user->getName()),
                            'date' => locale()->formatDatetime($view->review->added_datetime, auth()->user()->timezone),
                        ]); ?></span>
                    </div>

                    <div class="row mb-2">
                        <div class="col-12 col-lg-9">
                            <h3 class="text-medium display-5">
                                <?php echo e($view->review->title); ?>
                                <?php if (null === $view->review->active) {
                                    echo view('misc/status', [
                                        'type' => 'review',
                                        'status' => $view->review->active,
                                    ]);
                                } ?>
                            </h3>
                        </div>
                        <div class="col-12 col-lg-3 text-lg-right">
                            <a href="#reply" class="btn btn-primary btn-lg"><?php echo __('review.comment.button.create'); ?></a>
                        </div>
                    </div>
                    <div class="m-0 mb-3 text-warning text-nowrap display-11">
                        <?php echo $view->review->getOutputableValue('_rating'); ?>
                    </div>
                    <div class="mb-4">
                        <span class="display-11"><?php echo __('review.label.linked_listing'); ?>: <a href="<?php echo route($view->review->listing->type->slug . '/' . $view->review->listing->slug); ?>" target="_blank"><?php echo $view->review->listing->title; ?></a></span>
                    </div>
                    <div class="account-review-body card shadow-md bg-warning-light border-0 p-4 mb-5">
                        <div class="card-text display-11">
                            <p><?php echo \nl2br(e($view->review->description)); ?></p>

                            <?php if ($view->review->getOutputableForm()->getFields()->count() > 0) { ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <?php foreach ($view->review->getOutputableForm()->setValues($view->review->data->pluck('value', 'field_name')->all())->getFields() as $field) { ?>
                                            <?php $data = $view->review->data->where('field_name', $field->name)->first(); ?>
                                            <?php if (null !== $data && '' != $data->value && '' != ($value = $field->getOutputableValue())) { ?>
                                            <tr>
                                                <th scope="row"><?php echo (null !== $field->getIcon() ? '<i class="' . $field->getIcon() . ' fa-fw text-secondary"></i> ' : '') . ('' != $field->getLabel() ? e($field->getLabel()) . ':' : ''); ?></th>
                                                <td><?php echo $value; ?></td>
                                            </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php } ?>

                            <?php
                                if ('' != $view->review->attachment_id) {
                                    $attachments = $view->review->attachments()->get();

                                    if ($attachments->count() > 0) {
                                        echo '<div class="alert alert-light">';

                                        foreach ($attachments as $attachment) {
                                            echo '<a href="' . $attachment->getUrl() . '">' . e($attachment->name) . '.' . e($attachment->extension) . '</a><br />';
                                        }

                                        echo '</div>';
                                    }
                                }
                            ?>
                        </div>
                    </div>
                    <?php foreach ($view->comments as $comment) { ?>
                    <div class="mb-5">
                        <div class="text-dark display-10 mb-2">
                            <i class="fas fa-user-circle"></i> <strong><?php echo $comment->user->getName(); ?></strong> <?php echo locale()->formatDatetimeDiff($comment->added_datetime); ?>:
                            <?php if (null == $comment->active) {
                                echo view('misc/status', [
                                    'type' => 'review',
                                    'status' => $comment->active,
                                ]);
                            } ?>
                        </div>
                        <div class="card shadow-md border-0 p-4">
                            <div class="card-text display-11">                        
                                <div><?php echo \nl2br(e($comment->description)); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="pb-3">
                        <?php echo $view->comments->links(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-light pt-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <a name="reply"></a>
                <h3 class="text-bold display-6 mb-5"><?php echo __('review.comment.heading'); ?></h3>
                <?php echo $view->form->render('form/vertical'); ?>                    
            </div>
        </div>
    </div>
</div>
