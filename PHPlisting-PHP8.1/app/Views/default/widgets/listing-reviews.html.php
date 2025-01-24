<section class="widget listing-reviews-widget <?php echo $view->settings->colorscheme; ?> py-6">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <p class="text-caption text-black text-uppercase display-11 l-space-1"><?php echo e($view->settings->heading); ?></p>
                <h3 class="text-black display-5 l-space-0"><?php echo e($view->settings->description); ?></h3>
            </div>
            <?php if (null !== $view->settings->show_summary) { ?>
            <div class="col-12 py-5 border-bottom">
                <div class="row rating-page">
                    <div class="col-12 col-lg-3 mb-5 mb-lg-0">
                        <div class="text-center">
                            <span class="display-0 text-bold"><?php echo e($view->data->listing->rating); ?></span>
                            <p class="m-0 my-2 display-10 text-warning text-nowrap">
                                <?php echo $view->data->listing->getOutputableValue('_rating'); ?>
                            </p>
                            <span class="display-9"><?php echo e($view->data->listing->review_count); ?> <?php echo e(__('review.label.total')); ?></span>
                            <p class="pt-3"><a class="btn btn-primary" href="<?php echo route($view->data->listing->type->slug . '/' . $view->data->listing->slug . '/add-review'); ?>" role="button"><?php echo e(__('review.label.add_review')); ?></a></p>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5 mx-auto mb-5 mb-lg-0">
                        <div class="w-85">
                            <?php for ($i = 5; $i >= 1; $i--) { ?>
                            <div class="row align-items-center mb-2">
                                <?php $records = $view->averages->where('rating', '>=', $i)->where('rating', '<', $i + 1); ?>
                                <div class="col-auto col-lg-4"><?php echo e(__('review.label.x_stars', ['count' => $i], $i)); ?></div>
                                <div class="col col-lg-8 p-0">
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo round($records->sum('total') / $view->reviews->getTotal() * 100); ?>%;" aria-valuemin="0" aria-valuemax="100"><?php echo $records->sum('total'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if ($view->categories->count() > 0) { ?>
                    <div class="col-12 col-lg-4">
                        <h5 class="display-8 text-medium"><?php echo e(__('review.label.average_by_category')); ?></h5>
                        <ul class="list-unstyled mt-3">
                            <?php foreach ($view->categories as $category) { ?>
                            <li class="py-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span><?php echo e($category->name); ?></span>
                                    <p class="m-0 text-warning text-nowrap display-10">
                                        <?php echo view('misc/rating', ['rating' => round($view->categoryAverages->get('average_' . $category->id, 0), 1)]); ?>
                                    </p>
                                </div>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
            <div class="col-12 py-5">
                <?php $count = 0; ?>
                <?php foreach ($view->reviews as $review) { ?>
                    <div class="media d-block d-sm-flex py-5<?php $count++; if ($view->reviews->count() != $count) echo ' border-bottom'; ?>">
                        <div class="text-center mb-4">
                            <div class="bg-primary-light user-profile-icon rounded-circle d-flex align-items-center justify-content-center display-2 text-regular mr-3">
                                <span class="text-primary">
                                    <?php echo \mb_strtoupper(\mb_substr($review->user->first_name, 0, 1)); ?>
                                </span>
                            </div>
                        </div>
                        <div class="media-body">
                            <div class="row">
                                <div class="col-12 col-md-9 col-lg-10 order-lg-0 order-md-0 order-sm-1
                                    order-1 mb-4 mb-md-0">
                                    <div class="row justify-content-between mb-4">
                                        <div class="col-12 col-lg-10 col-md-9">
                                            <h5 class="display-8 text-medium mb-2">
                                                <?php echo e($review->title); ?>
                                                <span class="ml-2 text-secondary text-regular display-11"><?php echo e(__('review.label.by', ['author' => e($review->user->getName())])); ?></span>
                                            </h5>
                                            <p class="m-0 text-warning text-nowrap display-10 mb-2">
                                                <?php echo $review->getOutputableValue('_rating'); ?>
                                            </p>
                                        </div>
                                        <div class="col-12 col-lg-2 col-md-3 mb-3 text-md-right pl-md-0">
                                            <span class="text-secondary display-11"><?php echo locale()->formatDatetimeDiff($review->added_datetime); ?></span>
                                        </div>
                                    </div>
                                    <?php if (null === $review->active) {
                                        echo view('flash/primary', ['message' => e(__('review.alert.awaiting_moderation'))]);
                                    } ?>
                                    <p><?php echo \nl2br(e($review->description)); ?></p>
                                    <?php if ($review->getOutputableForm()->getFields()->count() > 0) { ?>
                                    <div class="table-responsive my-3">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <?php foreach ($review->getOutputableForm()->setValues($review->data->pluck('value', 'field_name')->all())->getFields() as $field) { ?>
                                                    <?php $data = $review->data->where('field_name', $field->name)->first(); ?>
                                                    <?php if (null !== $data && '' != $data->value && '' != ($value = $field->getOutputableValue())) { ?>
                                                    <tr>
                                                        <th scope="row" class="px-0"><?php echo (null !== $field->getIcon() ? '<i class="' . $field->getIcon() . ' fa-fw text-secondary"></i> ' : '') . ('' != $field->getLabel() ? e($field->getLabel()) . ':' : ''); ?></th>
                                                        <td><?php echo $value; ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php } ?>
                                    <div class="d-flex justify-content-md-end mt-4">
                                        <div>
                                             <?php if (false !== auth()->check()) { ?>
                                                <?php if ($view->data->listing->user_id == auth()->user()->id || $review->user_id == auth()->user()->id) { ?>
                                                    <a class="btn btn-sm btn-outline-secondary" href="<?php echo route('account/reviews/' . $review->id); ?>">
                                                        <?php echo e(__('review.comment.button.create')); ?>
                                                    </a>
                                                <?php } ?>
                                             <?php } ?>
                                        </div>
                                    </div>
                                    <?php $comments = $review->comments()->whereNotNull('active')->get(); ?>
                                    <?php if ($comments->count() > 0) { ?>
                                    <div class="comments-box position-relative <?php echo ('bg-light' == $view->settings->colorscheme) ? 'bg-white' : 'bg-light'; ?> mt-5">
                                        <?php $count = 0; ?>
                                        <?php foreach ($comments as $comment) { ?>
                                            <div class="row justify-content-between p-2 py-md-4 px-md-3">
                                                <div class="col-9 col-md-10">
                                                    <div class="text-medium display-10">
                                                        <?php echo e($comment->user->getName()); ?>
                                                        <span class="text-secondary">
                                                            <?php if ($view->data->listing->user_id == $comment->user_id) { ?>
                                                                (<?php echo e(__('review.label.owner')); ?>)
                                                            <?php } ?>
                                                        </span>
                                                        <p class="text-regular mt-1 display-11">
                                                            <?php echo \nl2br(e($comment->description)); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-3 col-md-2">
                                                    <small class="float-right"><?php echo locale()->formatDatetimeDiff($comment->added_datetime); ?></small>
                                                </div>
                                            </div>
                                            <?php $count++; if ($review->comments->count() != $count) { ?>
                                                <hr class="mx-3">
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <?php } ?>
                                </div>
                                <?php if ($view->categories->count() > 0) { ?>
                                <div class="col-12 col-md-3 col-lg-2 order-lg-1 order-md-1 order-sm-0 order-0 mb-4 mb-md-0">
                                    <?php foreach ($view->categories as $category) { ?>
                                        <?php if (null !== $review->get('rating_' . $category->id)) { ?>
                                            <div class="mb-3">
                                                <p class="display-11 m-0"><?php echo e($category->name); ?></p>
                                                <p class="m-0 text-warning text-nowrap display-10">
                                                    <?php echo view('misc/rating', ['rating' => $review->get('rating_' . $category->id)]); ?>
                                                </p>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div>
                    <?php echo $view->reviews->links(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
