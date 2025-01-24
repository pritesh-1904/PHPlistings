<?php
    $logo = false;
    
    if ($view->listing->hasRelation('logo') && false !== $view->listing->logo->isImage()) {
        $logo = $view->listing->logo;
    } else if ('' != $view->settings->get('default_logo', '') && null !== $file = \App\Models\File::where('document_id', $view->settings->default_logo)->first()) {
        if (false !== $file->isImage()) {
            $logo = $file;
        }
    }

    if (false !== $logo) {
        $attributes = attr([
            'src' => $logo->small()->getUrl(),
            'width' => $logo->small()->getWidth(),
            'height' => $logo->small()->getHeight(),
            'class' => 'img-fluid w-100 rounded-top',
            'loading' => 'lazy',
            'alt' => e($logo->title),
        ]);
    }

    $link = false;
    
    if (null !== $view->listing->get('_page')) {
        $link = route($view->type->slug . '/' . $view->listing->slug);
    }

    $outputableFormFields = $view->listing->getOutputableSearchResultForm()->setTimezone($view->listing->timezone)->setValues($view->listing->data->pluck('value', 'field_name')->all())->getFields();
?>
<div class="card listing-card h-100 border-0 shadow-md bg-white">
    <div class="row no-gutters">
        <?php if (false !== $logo) { ?>
        <div class="listing-card-img col-12">
            <?php if (false !== $link) { ?>
                <a class="d-block" href="<?php echo $link; ?>">
            <?php } ?>
                <img <?php echo $attributes; ?> />
            <?php if (false !== $link) { ?>
                </a>
            <?php } ?>
        </div>
        <?php } ?>
        <div class="<?php echo (false !== $logo ? 'listing-card-body' : 'listing-card-body-wide'); ?> col-12">
            <div class="card-body p-0 p-3">
                <div class="row">
                    <div class="col-12">
                        <p class="text-secondary m-0 mb-3 display-12">
                            <a href="<?php echo route($view->listing->type->slug); ?>"><?php echo e($view->listing->type->name_plural); ?></a> &raquo; <?php echo $view->listing->getOutputableValue('_category_links'); ?>
                        </p>
                        <h4 class="text-bold display-9 m-0 mb-3">
                            <?php if (false !== $link) { ?>
                                <a class="text-dark" href="<?php echo route($view->type->slug . '/' . $view->listing->slug); ?>">
                            <?php } ?>
                            <?php echo e($view->listing->getOutputableValue('_title')); ?>
                            <?php if ('Event' == $view->type->type && null !== $view->listing->get('event_date')) { ?>
                                <?php $start = \DateTime::createFromFormat('Y-m-d H:i:s', $view->listing->get('event_start_datetime')); ?>
                                <?php $end = \DateTime::createFromFormat('Y-m-d H:i:s', $view->listing->get('event_end_datetime')); ?>
                                - <?php echo locale()->formatDate($view->listing->get('event_date')); ?>
                            <?php } ?>
                            <?php if (false !== $link) { ?>
                                </a>
                            <?php } ?>
                        </h4>
                        <?php if (null !== $view->type->reviewable && null !== $view->listing->get('_reviews') && $view->listing->review_count > 0) { ?>
                            <p class="m-0 mb-3 text-warning text-nowrap display-11" title="<?php echo e(__('listing.search.block.label.rating', ['stars' => e($view->listing->rating), 'reviews' => e($view->listing->review_count)], (int) $view->listing->review_count)); ?>">
                                <?php echo $view->listing->getOutputableValue('_rating'); ?>
                            </p>
                        <?php } ?>
                    </div>
                    <div class="col-12 display-11">
                        <?php if (null !== $view->type->localizable && null !== $view->listing->get('_address')) { ?>
                            <p class="text-secondary m-0 mb-3"><i class="fas fa-map-marker-alt pr-2 fa-fw text-danger"></i>
                                <?php echo e(\strip_tags($view->listing->getOutputableValue('_address'))); ?>
                            </p>
                        <?php } ?>
                        <?php if ('Event' == $view->type->type && null !== $view->listing->get('event_date')) { ?>
                        <p class="text-secondary m-0 mb-3"><i class="fas fa-calendar-alt pr-2 fa-fw"></i>
                            <?php echo locale()->formatDate($view->listing->get('event_date')); ?>, <?php echo locale()->formatTime($start->format('H:i:s')); ?> - <?php echo locale()->formatTime($end->format('H:i:s')); ?>
                        </p>
                        <?php } ?>
                        <?php if ('Offer' == $view->type->type) { ?>
                        <p class="text-secondary m-0 mb-3"><i class="fas fa-tag pr-2 fa-fw text-info"></i>
                            <?php
                                $then = new \DateTime($view->listing->get('offer_end_datetime'), new \DateTimeZone($view->listing->timezone));
                                $now = new \DateTime('now', new \DateTimeZone('+0000'));

                                if ($now < $then) {
                                    echo e(__('listing.label.offer_expires_in_period', ['period' => locale()->formatDatetimeDiffPlain($view->listing->get('offer_end_datetime'), $view->listing->timezone)]));
                                } else {
                                    echo e(__('listing.label.offer_expired'));
                                }
                            ?>
                        </p>
                        <?php } ?>
                        <p class="m-0 mb-3"><?php echo e($view->listing->getOutputableValue('_short_description')); ?></p>
                        <div class="mb-2">
                            <?php
                                foreach ($outputableFormFields as $field) {
                                    if ($field instanceof \App\Src\Form\Type\Social) {
                                        $data = $view->listing->data->where('field_name', $field->name)->first();

                                        if (null !== $data && null !== $data->active && '' != $data->value && '' != ($value = $field->getOutputableValue())) {
                                            echo $value;
                                        }
                                    }
                                }
                            ?>
                        </div>                                
                        <div class="m-0">
                            <?php foreach ($outputableFormFields as $field) { ?>
                                <?php if ($field instanceof \App\Src\Form\Type\Social) continue; ?>
                                <?php $data = $view->listing->data->where('field_name', $field->name)->first(); ?>
                                <?php if (null !== $data && null !== $data->active && '' != $data->value && '' != ($value = $field->getOutputableValue())) { ?>
                                    <div class="mb-2">
                                        <?php if (null !== $field->getIcon()) { ?>
                                            <i class="<?php echo $field->getIcon(); ?> fa-fw text-secondary"></i> 
                                        <?php } ?>
                                        <strong><?php echo ('' != $field->getLabel() ? e($field->getLabel()) . ':' : ''); ?></strong> <?php echo $value; ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer border-0 pt-2 pb-3 px-3 bg-white">
                <?php if (null !== $view->listing->get('_page')) { ?>
                    <a href="<?php echo route($view->type->slug . '/' . $view->listing->slug); ?>" class="btn btn-primary"><?php echo e(__('listing.search.block.label.read_more')); ?></a>
                    <?php if (null !== $view->bookmarking) { ?>
                        <?php if (false === auth()->check()) { ?>
                            <a href="<?php echo route('account/login'); ?>" class="btn float-right"><?php echo view('misc/bookmark'); ?></a>
                        <?php } else { ?>
                            <span class="btn float-right" data-action="bookmark" data-id="<?php echo $view->listing->id; ?>" data-url="<?php echo route('ajax/bookmark'); ?>">
                               <?php echo view('misc/bookmark', ['state' => ($view->data->bookmarks->contains('listing_id', $view->listing->id)) ? 'on' : 'off']); ?>
                            </span>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
