<?php
    $logo = false;
    
    if (null !== $view->settings->get('show_logo')) {
        if ($view->review->listing->hasRelation('logo') && false !== $view->review->listing->logo->isImage()) {
            $logo = $view->review->listing->logo;
        } else if ('' != $view->settings->get('default_logo', '') && null !== $file = \App\Models\File::where('document_id', $view->settings->default_logo)->first()) {
            if (false !== $file->isImage()) {
                $logo = $file;
            }
        }
    }

    if (false !== $logo) {
        $attributes = attr([
            'src' => $logo->small()->getUrl(),
            'width' => $logo->small()->getWidth(),
            'height' => $logo->small()->getHeight(),
            'class' => 'img-fluid w-100',
            'loading' => 'lazy',
            'alt' => e($logo->title),
        ]);
    }
    
    $link = false;
    
    if (null !== $view->review->listing->get('_page')) {
        $link = route($view->type->slug . '/' . $view->review->listing->slug);
    }

    $outputableFormFields = $view->review->getOutputableSearchResultForm()->setTimezone($view->review->listing->timezone)->setValues($view->review->data->pluck('value', 'field_name')->all())->getFields();
?>
<div class="card review-card h-100 border-0 shadow-md bg-white">
    <div class="row no-gutters">
        <?php if (false !== $logo) { ?>
        <div class="review-card-img col-12 duo-fill-light">
            <?php if (false !== $link) { ?>
                <a class="d-block" href="<?php echo $link; ?>">
            <?php } ?>
                <img <?php echo $attributes; ?> />
            <?php if (false !== $link) { ?>
                </a>
            <?php } ?>
        </div>
        <?php } ?>
        <div class="<?php echo (false !== $logo ? 'review-card-body' : 'review-card-body-wide'); ?> col-12">
            <div class="card-body p-0 p-3">
                <div class="row">
                    <div class="col-12">
                        <p class="text-secondary m-0 mb-3 display-12">
                            <a href="<?php echo route($view->review->listing->type->slug); ?>"><?php echo e($view->review->listing->type->name_plural); ?></a> &raquo; <?php echo $view->review->listing->getOutputableValue('_category_links'); ?>
                        </p>
                        <h4 class="text-bold display-9 m-0 mb-3">
                            <?php if (false !== $link) { ?>
                                <a class="text-dark" href="<?php echo route($view->type->slug . '/' . $view->review->listing->slug); ?>">
                            <?php } ?>
                            <?php echo e($view->review->listing->getOutputableValue('_title')); ?>
                            <?php if (false !== $link) { ?>
                                </a>
                            <?php } ?>
                        </h4>
                    </div>
                    <div class="col-12 display-11">
                        <p class="m-0 mb-3 text-warning text-nowrap display-11">
                            <?php echo $view->review->getOutputableValue('_rating'); ?>
                        </p>
                        <p class="m-0 mb-3"><i>
                            <?php echo e($view->review->getOutputableValue('_description', $view->settings->get('length', 0))); ?>
                            <?php if (null !== $view->review->listing->get('_page')) { ?>
                                <a href="<?php echo route($view->type->slug . '/' . $view->review->listing->slug); ?>"><?php echo e(__('review.search.block.label.read_more')); ?></a>
                            <?php } ?>
                        </i></p>
                        <p class="display-12"><?php echo __('review.details_alt', ['by' => e($view->review->user->getName()), 'date' => locale()->formatDatetimeDiff($view->review->added_datetime)]); ?></p>
                        <div class="m-0">
                            <?php foreach ($outputableFormFields as $field) { ?>
                                <?php $data = $view->review->data->where('field_name', $field->name)->first(); ?>
                                <?php if (null !== $data && '' != $data->value && '' != ($value = $field->getOutputableValue())) { ?>
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
        </div>
    </div>
</div>
