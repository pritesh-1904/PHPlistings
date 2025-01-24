<?php
    $logo = false;
    
    if (null !== $view->settings->show_logo) {
        if ('' != $view->category->get('logo_id', '') && null !== $view->category->logo && false !== $view->category->logo->isImage()) {
            $logo = $view->category->logo;
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
            'class' => 'img-fluid rounded-top w-100',
            'loading' => 'lazy',
            'alt' => e($logo->title),
        ]);
    }
?>
<div class="card h-100 border-0 shadow-md">
    <?php if (false !== $logo) { ?>
    <a class="d-block" href="<?php echo route($view->type->slug . '/' . $view->category->slug); ?>">
        <div class="card-img-top">
            <img <?php echo $attributes; ?> />
        </div>
    </a>
    <?php } ?>
    <div class="card-body">
        <div class="my-3<?php if (null !== $view->settings->center) echo ' text-center'; ?>">
            <?php if (null !== $view->settings->show_icon) { ?>
                <div class="mb-4"><i class="<?php echo e($view->category->icon); ?> text-primary display-4"></i></div>
            <?php } ?>
            <h4 class="card-title text-bold display-10">
                <a class="text-super-dark" href="<?php echo route($view->type->slug . '/' . $view->category->slug); ?>">
                    <?php echo e($view->category->name); ?>
                </a>
                <?php if (null !== $view->settings->show_count && null !== $view->category->counter) { ?>
                    <span class="badge badge-light ml-2"><?php echo e($view->category->counter); ?></span>
                <?php } ?>
            </h4>

            <?php if (null !== $view->settings->show_description) { ?>
                <p class="display-11 mt-3"><?php echo e($view->category->short_description); ?></p>
            <?php } ?>
            <?php if (null !== $view->settings->show_children) { ?>
                <?php $children = []; ?>
                <?php foreach ($view->category->children as $child) { ?>
                    <?php if (null !== $view->settings->show_count && null !== $child->counter) { ?>
                        <?php $children[] = '<a class="my-1 display-11" href="' . route($view->type->slug . '/' . $child->slug) . '">' . e($child->name) . ' <span class="badge badge-light">' . e($child->counter) . '</span></a>'; ?>
                    <?php } else { ?>
                        <?php $children[] = '<a class="my-1 display-11" href="' . route($view->type->slug . '/' . $child->slug) . '">' . e($child->name) . '</a>'; ?>
                    <?php } ?>                    
                    <?php if ($view->settings->limit_children > 0 && count($children) >= $view->settings->limit_children) { break; } ?>
                <?php } ?>
                <?php echo implode(', ', $children); ?>
            <?php } ?>
        </div>
    </div>
</div>
