<div class="container">
    <div class="row py-6">
        <?php echo view('account/menu'); ?>
        <div class="col-12 col-lg-9 bd-box pl-lg-4">
            <div class="row">
                <div class="col-12 mb-5">            
                    <?php echo $view->alert ?? null; ?>
                    <div class="mb-3">
                        <span class="text-dark display-11"><?php echo __('message.details', [
                            'by' => e($view->message->sender->getName()),
                            'date' => locale()->formatDatetime($view->message->added_datetime, auth()->user()->timezone),
                        ]); ?></span>
                    </div>

                    <div class="row mb-2">
                        <div class="col-12 col-lg-9">
                            <h3 class="text-medium display-5">
                                <?php echo e($view->message->title); ?>
                                <?php if (null === $view->message->active) {
                                    echo view('misc/status', [
                                        'type' => 'message',
                                        'status' => $view->message->active,
                                    ]);
                                } ?>
                            </h3>
                        </div>
                        <div class="col-12 col-lg-3 text-lg-right">
                            <a href="#reply" class="btn btn-primary btn-lg"><?php echo __('message.reply.button.create'); ?></a>
                        </div>
                    </div>
                    <?php if (null !== $view->message->listing_id && null !== $view->message->listing) { ?>
                    <div class="mb-4">
                        <span class="display-11"><?php echo __('message.label.linked_listing'); ?>: <a href="<?php echo route($view->message->listing->type->slug . '/' . $view->message->listing->slug); ?>" target="_blank"><?php echo e($view->message->listing->title); ?></a></span>
                    </div>
                    <?php } ?>
                    <div class="account-message-body card shadow-md bg-warning-light border-0 p-4 mb-5">
                        <div class="card-text display-11">
                            <p><?php echo \nl2br(e($view->message->description)); ?></p>

                            <?php if ($view->message->getOutputableForm()->getFields()->count() > 0) { ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <?php foreach ($view->message->getOutputableForm()->setValues($view->message->data->pluck('value', 'field_name')->all())->getFields() as $field) { ?>
                                            <?php $data = $view->message->data->where('field_name', $field->name)->first(); ?>
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
                                if ('' != $view->message->attachment_id) {
                                    $attachments = $view->message->attachments()->get();

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
                    <?php foreach ($view->replies as $reply) { ?>
                    <div class="mb-5">
                        <div class="text-dark display-10 mb-2">
                            <i class="fas fa-user-circle"></i> <strong><?php echo $reply->user->getName(); ?></strong> <?php echo locale()->formatDatetimeDiff($reply->added_datetime); ?>:
                            <?php if (null === $reply->active) {
                                echo view('misc/status', [
                                    'type' => 'message',
                                    'status' => $reply->active,
                                ]);
                            } ?>                            
                        </div>
                        <div class="card shadow-md border-0 p-4">
                            <div class="card-text display-11">                        
                                <div><?php echo \nl2br(e($reply->description)); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="pb-3">
                        <?php echo $view->replies->links(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <a name="reply"></a>
                <h3 class="text-bold display-5 mb-5"><?php echo __('message.reply.heading'); ?></h3>
                <?php echo $view->form->render('form/vertical'); ?>
            </div>
        </div>
    </div>
</section>
