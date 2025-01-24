<section class="widget user-widget <?php echo $view->settings->colorscheme; ?> py-6">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <p class="text-caption text-black text-uppercase display-11 l-space-1"><?php echo e($view->settings->caption); ?></p>
                <h3 class="text-black display-5 l-space-0"><?php echo e($view->settings->heading); ?></h3>
            </div>
            <div class="col-12 mt-5" itemscope itemtype="http://schema.org/Person">
                <h5 itemprop="name" class="display-7 mb-4"><?php echo $view->data->listing->user->getName(); ?></h5>
                <?php if (null !== $view->settings->types && $view->types->count() > 0) { ?>
                    <?php foreach ($view->types as $type) { ?>
                        <?php if ($type->counter > 0) { ?>
                            <a href="<?php echo route($type->slug . '/search', ['user_id' => $view->data->listing->user->id]); ?>" class="btn mb-1 mb-sm-2 btn-secondary" title="<?php echo e($type->name_plural); ?>">
                               <i class="<?php echo e($type->icon); ?> display-10 mr-11"></i> <?php echo e($type->name_plural); ?> <span class="badge badge-light"><?php echo $type->counter; ?></span>
                            </a>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>

                <?php if (null !== $view->settings->fields) { ?>
                    <?php $fields = collect(); ?>
                    <?php foreach ($view->data->listing->user->getOutputableForm()->setTimezone($view->data->listing->user->timezone)->setValues($view->data->listing->user->data->pluck('value', 'field_name')->all())->getFields() as $field) { ?>
                        <?php $data = $view->data->listing->user->data->where('field_name', $field->name)->first(); ?>
                        <?php if (null !== $data && '' != $data->value && '' != ($value = $field->getOutputableValue())) { ?>
                            <?php $fields->push('
                            <tr>
                                <th scope="row" class="w-35 pl-0">' . (null !== $field->getIcon() ? '<i class="' . $field->getIcon() . ' fa-fw text-secondary"></i> ' : '') . ('' != $field->getLabel() ? e($field->getLabel()) . ':' : '') . '</th>
                                <td class="pr-0">' . $value . '</td>
                            </tr>
                            '); ?>
                        <?php } ?>
                    <?php } ?>
                    <?php if ($fields->count() > 0) { ?>
                        <div class="mb-4 py-2">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <?php echo $fields->implode(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</section>
