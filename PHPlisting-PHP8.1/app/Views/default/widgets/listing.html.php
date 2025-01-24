<?php
    $field = $view->data->listing->data->where('field_name', 'opening_hours_id')->first();
    if (null !== $field && null !== $field->active && '' != $field->get('value', '')) {
        $hours = \App\Models\Hour::where('hash', $field->value)->orderBy('dow')->orderBy('start_time')->get(['dow', 'start_time', 'end_time']);
    }

    $outputableFormFields = $view->data->listing
        ->getOutputableForm()
        ->setTimezone($view->data->listing->timezone)
        ->setValues($view->data->listing->data->pluck('value', 'field_name')->all())
        ->getFields();

    if ('Offer' == $view->data->listing->type->type) {
        $offerExpirationTimestamp = (new \DateTime($view->data->listing->offer_end_datetime, new \DateTimeZone($view->data->listing->timezone)))->setTimezone(new \DateTimeZone('+0000'))->format('U');
        
        layout()->addFooterJs('<script src="' . asset('js/countdown/jquery.countdown.min.js') . '"></script>');
        layout()->addFooterJs('<script>
            $(document).ready(function() {

                $(\'#countdown\').countdown(\'' . ($offerExpirationTimestamp * 1000) . '\')
                .on(\'update.countdown\', function (event) {
                    var format = \'<span class="display-11 font-weight-normal">' . e(__('listing.label.offer_expires_in')) . '</span><br />\'+
                        \'<span class="display-9">%-D %!D:' . e(__('datetime.day', [], 1)) . ',' . e(__('datetime.day', [], 2)) . '; %H:%M:%S</span>\';

                    $(this).html(event.strftime(format));
                })
                .on(\'finish.countdown\', function (event) {
                    $(this)
                        .html(\'' . e(__('listing.alert.offer_expired')) . '\')
                        .parent()
                        .addClass(\'disabled\');
                });
            });
        </script>');
    }

    layout()->addCss('<link href="' . asset('js/simplelightbox/simplelightbox.min.css') . '" rel="stylesheet">');
    layout()->addFooterJs('<script src="' . asset('js/simplelightbox/simple-lightbox.min.js') . '"></script>');

    layout()->addCss('<link rel="stylesheet" href="' . asset('js/leaflet/leaflet.css') . '" />');
    layout()->addFooterJs('<script src="' . asset('js/leaflet/leaflet.js') . '"></script>');

    layout()->addCss('<link rel="stylesheet" href="' . asset('js/leaflet-fullscreen/leaflet.fullscreen.css') . '" />');
    layout()->addFooterJs('<script src="' . asset('js/leaflet-fullscreen/Leaflet.fullscreen.min.js') . '"></script>');

    layout()->addFooterJs('<script src="' . asset('js/leaflet-markers/L.Icon.FontAwesome.js') . '"></script>');
    layout()->addCss('<link rel="stylesheet" href="' . asset('js/leaflet-markers/L.Icon.FontAwesome.css') . '" />');

//    layout()->addCss('<link rel="stylesheet" href="' . asset('js/leaflet-routing-machine/leaflet-routing-machine.css') . '" />');
//    layout()->addFooterJs('<script src="' . asset('js/leaflet-routing-machine/leaflet-routing-machine.min.js') . '"></script>');
//    layout()->addFooterJs('<script src="' . asset('js/leaflet-routing-machine/Control.Geocoder.js') . '"></script>');
?>
<div itemscope itemtype="http://schema.org/<?php echo $view->data->listing->type->type; ?>">
    <section class="widget listing-widget <?php echo $view->settings->colorscheme; ?> py-6">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-lg-8">
                    <p class="text-caption link-success text-uppercase text-black display-11 l-space-1 mb-1">
                        <a href="<?php echo route($view->data->listing->type->slug); ?>"><?php echo e($view->data->listing->type->name_plural); ?></a> &raquo; <?php echo $view->data->listing->getOutputableValue('_category_links'); ?>
                    </p>
                    <h1 class="display-3 text-black mb-3">
                        <?php echo $view->data->listing->getOutputableValueWithSchema('_title'); ?>
                    </h1>
                    <div class="display-8 mb-3">
                        <?php if ($view->data->listing->isFeatured()) { ?>
                            <span class="badge badge-pill text-sm badge-danger"><?php echo e(__('listing.badge.featured')); ?></span>
                        <?php } ?>
                        <?php if ($view->data->listing->isNew()) { ?>
                            <span class="badge badge-pill text-sm badge-info"><?php echo e(__('listing.badge.new')); ?></span>
                        <?php } ?>
                        <?php if ($view->data->listing->isUpdated()) { ?>
                            <span class="badge badge-pill text-sm badge-secondary"><?php echo e(__('listing.badge.updated')); ?></span>
                        <?php } ?>
                        <?php if ($view->data->listing->isHot()) { ?>
                            <span class="badge badge-pill text-sm badge-warning"><?php echo e(__('listing.badge.hot')); ?></span>
                        <?php } ?>
                        <?php if (isset($hours) && $view->data->listing->isOpen($hours)) { ?>
                            <span class="badge badge-pill text-sm badge-success"><?php echo e(__('listing.badge.open_now')); ?></span>
                        <?php } ?>
                    </div>
                    <?php if (null !== $view->data->listing->type->reviewable && null !== $view->data->listing->get('_reviews') && $view->data->listing->review_count > 0) { ?>
                        <p class="m-0 mb-3 text-warning text-nowrap display-11" title="<?php echo e(__('listing.header.label.rating', ['stars' => e($view->data->listing->rating), 'reviews' => e($view->data->listing->review_count)], (int) $view->data->listing->review_count)); ?>">
                            <span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                                <?php echo $view->data->listing->getOutputableValue('_rating'); ?>
                                <span class="ml-2 text-dark">
                                    <?php echo __('listing.header.label.rating', ['stars' => '<span itemprop="ratingValue">' . e($view->data->listing->rating) . '</span>', 'reviews' => '<span itemprop="reviewCount">' . e($view->data->listing->review_count) . '</span>'], (int) $view->data->listing->review_count); ?>
                                </span>
                            </span>
                        </p>
                    <?php } else if ('Product' == $view->data->listing->type->type) { ?>
                        <span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                            <meta itemprop="ratingValue" content="5.0">
                            <meta itemprop="ratingCount" content="1">
                        </span>
                    <?php } ?>
                    <?php if (null !== $view->data->listing->type->localizable && null !== $view->data->listing->get('_address')) { ?>
                        <div class="d-flex align-items-baseline">
                            <div>
                                <i class="fas fa-map-marker-alt text-danger fa-fw"></i>
                            </div>
                            <div class="ml-2 text-secondary">
                                <?php if ('Event' == $view->data->listing->type->type) { ?>
                                <div itemprop="location" itemscope itemtype="https://schema.org/Place">
                                    <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                                        <?php echo $view->data->listing->getOutputableValueWithSchema('_address'); ?>
                                    </div>
                                </div>
                                <?php } else if ('JobPosting' == $view->data->listing->type->type) { ?>
                                <div itemprop="jobLocation" itemscope itemtype="https://schema.org/Place">
                                    <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                                        <?php echo $view->data->listing->getOutputableValueWithSchema('_address'); ?>
                                    </div>
                                </div>
                                <?php } else if ('Product' == $view->data->listing->type->type) { ?>
                                <div>
                                    <?php echo $view->data->listing->getOutputableValue('_address'); ?>
                                </div>
                                <?php } else { ?>
                                <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                                    <?php echo $view->data->listing->getOutputableValueWithSchema('_address'); ?>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-12 col-lg-4 text-center text-lg-right mt-5 mt-lg-0">
                    <?php if (null !== $view->data->listing->get('_page')) { ?>
                        <?php if (null !== $view->settings->sharing) { ?>
                        <div class="share-button btn btn-light btn-sm ml-1 mb-2 position-relative">
                            <i class="fas fa-share-alt"></i> <?php echo e(__('listing.header.label.share')); ?>
                            <div class="share-popup-screen"></div>
                            <div class="share-popup-wrapper">
                                <ul class="list-inline share-popup shadow-md">
                                    <li class="list-inline-item">
                                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(route($view->data->listing->type->slug . '/' . $view->data->listing->slug)); ?>" class="share-link btn btn-circle btn-icn btn-facebook" target="_blank" title="<?php echo e(__('listing.header.label.share_facebook')); ?>">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a href="https://twitter.com/intent/tweet?text=<?php echo $view->data->listing->getOutputableValue('_title'); ?>&url=<?php echo e(route($view->data->listing->type->slug . '/' . $view->data->listing->slug)); ?>" class="share-link btn btn-circle btn-icn btn-twitter" target="_blank" title="<?php echo e(__('listing.header.label.share_twitter')); ?>">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo route($view->data->listing->type->slug . '/' . $view->data->listing->slug); ?>" class="share-link btn btn-circle btn-icn btn-linkedin-in" target="_blank" title="<?php echo e(__('listing.header.label.share_linkedin')); ?>">
                                            <i class="fab fa-linkedin-in"></i>
                                        </a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a href="https://pinterest.com/pin/create/button/?url=<?php echo e(route($view->data->listing->type->slug . '/' . $view->data->listing->slug)); ?>" class="share-link btn btn-circle btn-icn btn-pinterest" target="_blank" title="<?php echo e(__('listing.header.label.share_pinterest')); ?>">
                                            <i class="fab fa-pinterest"></i>
                                        </a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a href="https://www.reddit.com/submit?title=<?php echo $view->data->listing->getOutputableValue('_title'); ?>&url=<?php echo e(route($view->data->listing->type->slug . '/' . $view->data->listing->slug)); ?>" class="share-link btn btn-circle btn-icn btn-reddit" target="_blank" title="<?php echo e(__('listing.header.label.share_reddit')); ?>">
                                            <i class="fab fa-reddit"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if (null !== $view->settings->bookmarking) { ?>
                            <?php if (false === auth()->check()) { ?>
                                <a href="<?php echo route('account/login'); ?>" class="btn btn-light btn-sm ml-1 mb-2"><?php echo view('misc/bookmark', ['type' => 'button']); ?></a>
                            <?php } else { ?>
                                <span class="bookmark-button btn btn-light btn-sm ml-1 mb-2" data-action="bookmark" data-type="button" data-id="<?php echo $view->data->listing->id; ?>" data-url="<?php echo route('ajax/bookmark'); ?>">
                                   <?php echo view('misc/bookmark', ['type' => 'button', 'state' => ($view->data->bookmarks->contains('listing_id', $view->data->listing->id)) ? 'on' : 'off']); ?>
                                </span>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                    <?php if (null !== $view->data->listing->type->reviewable && null !== $view->data->listing->get('_reviews')) { ?>
                        <?php if ($view->data->listing->review_count > 0) { ?>
                            <div class="btn-group ml-1 mb-2" role="group">
                                <a href="<?php echo route($view->data->listing->type->slug . '/' . $view->data->listing->slug . '/reviews'); ?>" class="btn btn-light btn-sm"><i class="far fa-star"></i> <?php echo e(__('listing.header.label.reviews')); ?></a>
                                <a href="<?php echo route($view->data->listing->type->slug . '/' . $view->data->listing->slug . '/add-review'); ?>" class="btn btn-light btn-sm"><i class="fas fa-plus"></i></a>
                            </div>
                        <?php } else { ?>
                            <a href="<?php echo route($view->data->listing->type->slug . '/' . $view->data->listing->slug . '/add-review'); ?>" class="btn btn-light btn-sm ml-1 mb-2"><i class="far fa-star"></i> <?php echo e(__('listing.header.label.reviews_add')); ?></a>
                        <?php } ?>
                    <?php } ?>
                    <?php if (null === $view->data->listing->claimed) { ?>
                        <a href="<?php echo route($view->data->listing->type->slug . '/' . $view->data->listing->slug . '/claim'); ?>" class="btn btn-light btn-sm ml-1 mb-2"><i class="fas fa-shield-alt"></i> <?php echo e(__('listing.header.label.claim')); ?></a>
                    <?php } ?>
                    <?php if (false !== auth()->check('user_login') && $view->data->listing->user_id == auth()->user()->id) { ?>
                        <a href="<?php echo route('account/manage/' . $view->data->listing->type->slug . '/summary/' . $view->data->listing->slug); ?>" class="btn btn-light btn-sm ml-1 mb-2"><i class="far fa-edit"></i> <?php echo e(__('listing.header.label.edit')); ?></a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
    <section class="widget listing-widget-body bg-white pb-6">
        <div class="container">
            <div class="row">
                <div class="col-12 py-5">
                    <div class="row">
                        <div class="col-12 col-lg-8 order-2 order-lg-1">
                            <div class="row">
                                <?php if (null !== session()->get('_search_' . $view->data->listing->type->slug)) { ?>
                                    <div class="col-12">
                                        <nav aria-label="breadcrumb">
                                            <ol class="breadcrumb p-0">
                                                <li class="breadcrumb-item"><a href="<?php echo session()->get('_search_' . $view->data->listing->type->slug); ?>"><i class="fas fa-angle-left fa-fw"></i> <?php echo e(__('listing.label.back_to_search_results')); ?></a></li>
                                            </ol>
                                        </nav>
                                    </div>
                                <?php } ?>

                                <?php $socialProfileLinks = collect(); ?>
                                <?php foreach ($outputableFormFields as $field) { ?>
                                    <?php if ($field instanceof \App\Src\Form\Type\Social) { ?>
                                        <?php $data = $view->data->listing->data->where('field_name', $field->name)->first(); ?>
                                        <?php if (null !== $data && null !== $data->active && '' != $data->value && '' != ($value = $field->getOutputableValueWithSchema())) { ?>
                                            <?php $socialProfileLinks->push($value); ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                                
                                <?php if ($socialProfileLinks->count() > 0) { ?>
                                    <div class="col-12 mb-4 py-4 border-bottom order-<?php echo e($view->settings->social_order); ?>">
                                        <h3 class="display-5 text-bold mb-4"><?php echo e($view->settings->social_heading); ?></h3>
                                        <?php echo $socialProfileLinks->implode(); ?>
                                    </div>
                                <?php } ?>
                                
                                <?php if (\mb_strlen($view->data->listing->getOutputableValue('_description'), 'UTF-8') > 0) { ?>
                                    <div class="col-12 mb-4 border-bottom pb-4 order-<?php echo e($view->settings->about_order); ?>">
                                        <h3 class="display-5 text-bold mb-4"><?php echo e($view->settings->about_heading); ?></h3>
                                        <p>
                                            <?php echo $view->data->listing->getOutputableValueWithSchema('_description'); ?>
                                        </p>
                                    </div>
                                <?php } ?>
                                <?php $fields = collect(); ?>
                                <?php $hasFields = false; ?>
                                <?php foreach ($outputableFormFields as $field) { ?>
                                    <?php if ($field instanceof \App\Src\Form\Type\Social) continue; ?>
                                    <?php $data = $view->data->listing->data->where('field_name', $field->name)->first(); ?>
                                    <?php if (false !== $field->isSeparator() || (null !== $data && null !== $data->active && '' != $data->value && '' != ($value = $field->getOutputableValueWithSchema()))) { ?>
                                        <?php if (false !== $field->isSeparator()) { ?>
                                            <?php
                                                if (false === $hasFields && null !== $fields->last()) {
                                                    $fields->pop();
                                                }

                                                $hasFields = false;
                                            ?>
                                            <?php $fields->push('
                                            <tr>
                                                <th colspan="2" class="pl-2 bg-light"><strong>' . (null !== $field->getIcon() ? '<i class="' . $field->getIcon() . ' fa-fw text-secondary"></i> ' : '') . e($field->getLabel()) . '</strong></th>
                                            </tr>
                                            '); ?>
                                        <?php } else { ?>
                                            <?php $fields->push('
                                            <tr>
                                                <th scope="row" class="w-35 pl-2">' . (null !== $field->getIcon() ? '<i class="' . $field->getIcon() . ' fa-fw text-secondary"></i> ' : '') . ('' != $field->getLabel() ? e($field->getLabel()) . ':' : '') . '</th>
                                                <td class="pr-0">' . $value . '</td>
                                            </tr>
                                            '); ?>
                                            <?php $hasFields = true; ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                                <?php if (false !== $hasFields) { ?>
                                    <div class="col-12 mb-4 py-4 border-bottom order-<?php echo e($view->settings->features_order); ?>">
                                        <h3 class="display-5 text-bold mb-4"><?php echo e($view->settings->features_heading); ?></h3>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tbody>
                                                    <?php echo $fields->implode(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php $field = $view->data->listing->data->where('field_name', 'gallery_id')->first(); ?>
                                <?php if ($view->data->listing->get('_gallery_size') > 0 && null !== $field && null !== $field->active && '' != $field->get('value', '')) { ?>
                                    <?php $images = \App\Models\File::where('document_id', $field->value)->limit((int) $view->data->listing->_gallery_size)->get(); ?>
                                    <?php if ($images->count() > 0) { ?>
                                        <div class="col-12 mb-4 border-bottom py-4 order-<?php echo e($view->settings->gallery_order); ?>">
                                            <h3 class="display-5 text-bold mb-4"><?php echo e($view->settings->gallery_heading); ?></h3>
                                            <div class="row no-gutters gallery">
                                                <?php foreach ($images as $image) { ?>
                                                    <?php if (false !== $image->isImage()) { ?>
                                                        <?php
                                                            $attributes = attr([
                                                                'src' => $image->small()->getUrl(),
                                                                'width' => $image->small()->getWidth(),
                                                                'height' => $image->small()->getHeight(),
                                                                'class' => 'img-fluid w-100',
                                                                'itemprop' => 'url',
                                                                'title' => e($image->title),
                                                                'alt' => e($image->title),
                                                            ]);
                                                        ?>
                                                        <div class="col-12 col-md-6 col-lg-4 px-1 mb-2">
                                                            <a href="<?php echo $image->large()->getUrl(); ?>">
                                                                <div itemprop="image" itemscope itemtype="http://schema.org/ImageObject">
                                                                    <img <?php echo $attributes; ?> />
                                                                </div>
                                                            </a>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php
                                            layout()->addFooterJs('<script>
                                                var lightbox = $(\'.gallery a\').simpleLightbox();
                                            </script>');
                                        ?>
                                    <?php } ?>
                                <?php } ?>
                                <?php if (null !== $view->data->listing->type->localizable && null !== $view->data->listing->get('_map')) { ?>
                                    <div class="col-12 mb-4 border-bottom py-4 order-<?php echo e($view->settings->location_order); ?>">
                                        <h3 class="display-5 text-bold mb-4"><?php echo e($view->settings->location_heading); ?></h3>
                                        <div id="map" class="map"></div>
                                    </div>
                                <?php } ?>
                                <?php $first = true; ?>
                                <?php $types = \App\Models\Type::whereNull('deleted')->orderBy('weight')->get(); ?>
                                <?php if ($view->related->count() > 0) { ?>
                                <div class="col-12 py-4 order-<?php echo e($view->settings->related_order); ?>">
                                    <h3 class="display-5 text-bold mb-4"><?php echo e($view->settings->related_heading); ?></h3>
                                    <ul class="nav nav-pills my-3" id="pills-tab" role="tablist">
                                        <?php foreach ($types as $type) { ?>
                                            <?php $count = $view->related->where('type_id', $type->id)->count(); ?>
                                            <?php if ($count > 0) { ?>
                                                <li class="nav-item">
                                                    <a class="nav-link<?php echo (false !== $first) ? ' active' : ''; ?>" id="pills-<?php echo $type->slug; ?>-tab" data-toggle="pill"
                                                        href="#pills-<?php echo $type->slug; ?>" role="tab" aria-controls="pills-<?php echo $type->slug; ?>"
                                                        aria-selected="true"><?php echo e($type->name_plural); ?> <span class="badge badge-pill badge-light"><?php echo $count; ?></span></a>
                                                </li>
                                            <?php $first = false; } ?>
                                        <?php } ?>
                                    </ul>
                                    <?php $first = true; ?>
                                    <div class="tab-content py-3" id="pills-tabContent">
                                        <?php foreach ($types as $type) { ?>
                                            <?php $count = $view->related->where('type_id', $type->id)->count(); ?>
                                            <?php if ($count > 0) { ?>
                                                <div class="tab-pane fade<?php echo (false !== $first) ? ' show active' : ''; ?>" id="pills-<?php echo $type->slug; ?>" role="tabpanel"
                                                    aria-labelledby="pills-<?php echo $type->slug; ?>-tab">
                                                    <div class="row">
                                                        <?php foreach ($view->related->where('type_id', $type->id) as $listing) { ?>
                                                        <div class="col-12 col-sm-6 mb-4">
                                                            <?php echo view('widgets/cards/listing', [
                                                                'listing' => $listing,
                                                                'type' => $listing->type,
                                                                'data' => $view->data,
                                                                'settings' => $view->settings,
                                                                'bookmarking' => $view->settings->related_bookmarking,
                                                            ]); ?>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            <?php $first = false; } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 order-1 order-lg-2">
                            <?php if ($view->data->listing->badges()->whereNotNull('active')->count() > 0) { ?>
                                <div class="mb-5">
                                    <?php foreach ($view->data->listing->badges()->whereNotNull('active')->orderBy('weight')->get() as $badge) { ?>
                                        <?php echo view('misc/badge', ['badge' => $badge]); ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php $field = $view->data->listing->data->where('field_name', 'logo_id')->first(); ?>
                            <?php if (null !== $field && null !== $field->active && '' != $field->get('value', '') && null !== $logo = \App\Models\File::where('document_id', $field->value)->first()) { ?>
                                <?php if (false !== $logo->isImage()) { ?>
                                    <?php
                                        $attributes = attr([
                                            'src' => $logo->small()->getUrl(),
                                            'width' => $logo->small()->getWidth(),
                                            'height' => $logo->small()->getHeight(),
                                            'class' => 'img-fluid w-100 rounded',
                                            'itemprop' => 'url',
                                            'alt' => e($logo->title),
                                        ]);
                                    ?>
                                    <div itemprop="image" itemscope itemtype="http://schema.org/ImageObject" class="mb-5">
                                        <img <?php echo $attributes; ?> />
                                    </div>
                                <?php } ?>
                            <?php } ?>

                            <?php if ('Offer' == $view->data->listing->type->type) { ?>
                            <div class="card shadow-md border-0 mb-5">
                                <div class="card-header py-4 border-0">
                                    <h4 class="text-bold display-8 my-1"><?php echo e(__('listing.heading.offer')); ?></h4>
                                </div>
                                <div class="card-body">
                                    <meta itemprop="availabilityStarts" content="<?php echo locale()->formatDatetimeISO8601($view->data->listing->offer_start_datetime, $view->data->listing->timezone); ?>"/>
                                    <meta itemprop="availabilityEnds" content="<?php echo locale()->formatDatetimeISO8601($view->data->listing->offer_end_datetime, $view->data->listing->timezone); ?>"/>
                                    <?php if ('' != $view->data->listing->offer_price) { ?>
                                    <div class="py-3 text-center">
                                        <span class="display-4 text-primary text-medium mr-2" itemprop="price"><?php echo e(locale()->formatPrice($view->data->listing->getOfferPriceWithDiscount())); ?></span>
                                        <span class="display-10 text-secondary text-medium"><strike><?php echo e(locale()->formatPrice($view->data->listing->offer_price)); ?></strike></span>
                                    </div>
                                    <?php } else { ?>
                                        <div class="display-4 py-3 text-center text-primary text-medium"><?php echo e($view->data->listing->getOfferDiscountDescription()); ?></div>
                                    <?php } ?>
                                    <?php if ('' != $view->data->listing->offer_terms) { ?>
                                    <div class="py-2 text-center">
                                        <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#offer_toc_modal"><?php echo e(__('listing.label.offer_toc')); ?></button>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="card-footer border-0 bg-secondary-light py-4">
                                    <span class="text-secondary text-bold display-9" id="countdown"></span>
                                    <span class="float-right">
                                        <i class="far fa-clock text-secondary text-bold display-6"></i>
                                    </span>                                
                                </div>
                            </div>
                            <?php } ?>
                            <?php if ('Event' == $view->data->listing->type->type) { ?>
                            <div class="card shadow-md border-0 mb-5">
                                <div class="card-header py-4 border-0">
                                    <h4 class="text-bold display-8 my-1"><?php echo e(__('listing.heading.event_dates')); ?></h4>
                                </div>
                                <div class="card-body">
                                    <meta itemprop="startDate" content="<?php echo locale()->formatDatetimeISO8601($view->data->listing->event_start_datetime, $view->data->listing->timezone); ?>"/>
                                    <meta itemprop="endDate" content="<?php echo locale()->formatDatetimeISO8601($view->data->listing->event_end_datetime, $view->data->listing->timezone); ?>"/>
                                    <table class="table">
                                        <tbody>
                                        <?php $dates = $view->data->listing->dates; ?>
                                        <?php $start = \DateTime::createFromFormat('Y-m-d H:i:s', $view->data->listing->get('event_start_datetime')); ?>
                                        <?php $end = \DateTime::createFromFormat('Y-m-d H:i:s', $view->data->listing->get('event_end_datetime')); ?>
                                        <?php $upcoming = $view->data->listing->getEventUpcomingDate($dates); ?>
                                        <?php foreach ($dates as $date) { ?>
                                            <?php if ($date->event_date == $upcoming) { ?>
                                            <tr class="table-primary">
                                                <th class="border-0 align-middle" scope="row"><?php echo locale()->formatDate($date->event_date); ?></th>
                                                <td class="border-0 text-right display-11 align-middle">
                                                    <?php echo locale()->formatTime($start->format('H:i:s')); ?> - <?php echo locale()->formatTime($end->format('H:i:s')); ?>
                                                </td>
                                            </tr>
                                            <?php } else { ?>
                                            <tr>
                                                <th scope="row"><?php echo locale()->formatDate($date->event_date); ?></th>
                                                <td class="text-right display-11">
                                                    <?php echo locale()->formatTime($start->format('H:i:s')); ?> - <?php echo locale()->formatTime($end->format('H:i:s')); ?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    </table>
                                </div>
                            </div>
                            <?php } ?>

                            <?php $field = $view->data->listing->data->where('field_name', 'opening_hours_id')->first(); ?>
                            <?php if (null !== $field && null !== $field->active && '' != $field->get('value', '')) { ?>
                            <?php $hours = \App\Models\Hour::where('hash', $field->value)->orderBy('dow')->orderBy('start_time')->get(['dow', 'start_time', 'end_time']); ?>
                            <?php if ($hours->count() > 0) { ?>
                            <?php $dow = (new \DateTime('now', new \DateTimeZone($view->data->listing->timezone)))->format('N'); ?>
                            <div class="card shadow-md border-0 mb-5">
                                <div class="card-header py-4 border-0">
                                    <h4 class="text-bold display-8"><?php echo e(__('listing.heading.opening_hours')); ?></h4>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tbody>
                                            <?php foreach (locale()->getDaysOfWeek() as $id => $day) { ?>
                                            <tr<?php echo $dow == $id ? ' class="table-primary"' : ''; ?>>
                                                <th scope="row"><?php echo $day; ?></th>
                                                <td class="text-right">
                                                    <?php if ($hours->where('dow', $id)->count() > 0) { ?>
                                                        <?php foreach ($hours->where('dow', $id) as $period) { ?>
                                                            <span itemprop="openingHours" content="<?php echo __('hour.dow.short.' . $id) . ' ' . locale()->formatTimeISO8601($period->start_time) . '-' . locale()->formatTimeISO8601($period->end_time); ?>">
                                                                <?php echo locale()->formatTime($period->start_time); ?> - <?php echo locale()->formatTime($period->end_time); ?><br />
                                                            </span>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <?php echo e(__('listing.label.opening_hours_closed_today')); ?>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php if (false !== $view->data->listing->isOpen($hours)) { ?>
                                <div class="card-footer border-0 bg-success-light py-4">
                                    <span class="text-success text-bold display-9">
                                        <?php echo e(__('listing.alert.open_now')); ?>
                                    </span>
                                    <span class="float-right">
                                        <i class="far fa-clock text-success text-bold display-6"></i>
                                    </span>
                                </div>
                                <?php } else { ?>
                                <div class="card-footer border-0 bg-secondary-light py-4">
                                    <span class="text-secondary text-bold display-9">
                                        <?php echo e(__('listing.alert.closed_now')); ?>
                                    </span>
                                    <span class="float-right">
                                        <i class="far fa-clock text-secondary text-bold display-6"></i>
                                    </span>
                                </div>
                                <?php } ?>
                            </div>
                            <?php } ?>
                            <?php } ?>

                            <?php
                                $phone = $view->data->listing->data->where('field_name', 'phone')->first();
                                $website = $view->data->listing->data->where('field_name', 'website')->first();

                                if (null !== $view->data->listing->get('_send_message') || (null !== $phone && null !== $phone->get('active') && '' != $phone->get('value', '')) || (null !== $website && null !== $website->get('active') && '' != $website->get('value', ''))) {
                            ?>
                            <div class="card shadow-md border-0 mb-5">
                                <div class="card-header py-4 border-0">
                                    <h4 class="text-bold display-8 my-1"><?php echo e(__('listing.heading.contacts')); ?></h4>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <?php if (null !== $view->data->listing->get('_send_message')) { ?>
                                        <li class="mb-3">
                                            <i class="fas fa-envelope text-primary mr-2"></i>
                                            <a href="<?php echo route($view->data->listing->type->slug . '/' . $view->data->listing->slug . '/send-message'); ?>">
                                                <span class="text-primary"><?php echo e(__('listing.label.send_message')); ?></span>
                                            </a>
                                        </li>
                                        <?php } ?>
                                        <?php if (null !== $phone && null !== $phone->active && '' != e($phone->value)) { ?>
                                        <li class="mb-3">
                                            <i class="fas fa-phone text-primary mr-2"></i>
                                            <span itemprop="telephone" content="<?php echo e($phone->value); ?>" class="text-primary" data-action="click-to-call" data-id="<?php echo e($view->data->listing->id); ?>" data-url="<?php echo route('ajax/click-to-call'); ?>"><?php echo e(__('listing.label.click_to_call')); ?></span>
                                        </li>
                                        <?php } ?>
                                        <?php if (null !== $website && null !== $website->active && '' != e($website->value)) { ?>
                                        <li class="mb-3">
                                            <i class="fas fa-globe text-primary mr-2"></i>
                                            <a<?php if (null === $view->data->listing->get('_dofollow')) echo ' rel="nofollow"'; ?> target="_blank" href="<?php echo route($view->data->listing->type->slug . '/' . $view->data->listing->slug . '/visit-website'); ?>">
                                                <span class="text-primary"><?php echo e(__('listing.label.visit_website')); ?></span>
                                            </a>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php if ('' != $view->data->listing->offer_terms) { ?>
<div class="modal fade" id="offer_toc_modal" tabindex="-1" role="dialog" aria-labelledby="offer_toc_modal_label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h7 class="modal-title" id="offer_toc_modal_label"><?php echo e(__('listing.heading.offer_toc')); ?></h7>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo e(__('listing.label.offer_toc_close')); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="display-12"><?php echo \nl2br(e($view->data->listing->offer_terms)); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('listing.label.offer_toc_close')); ?></button>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<?php
    if (null !== $view->data->listing->type->localizable) {
        layout()->addFooterJs('
        <script>
            $(document).ready(function() {
                $(\'#map\').listingMap({
                    latitude: \'' . $view->data->listing->latitude . '\',
                    longitude: \'' . $view->data->listing->longitude . '\',
                    zoom: \'' . $view->data->listing->zoom . '\',
                    icon_color: \'' . ($view->data->listing->category->icon_color ?? 'white') . '\',
                    marker_color: \'' . ($view->data->listing->category->marker_color ?? 'red') . '\',
                    class: \'' . $view->data->listing->category->icon . '\',
                    provider: \'' . config()->map->provider . '\',
                    accessToken: \'' . config()->map->access_token . '\',
                });
            });
        </script>');
    }

    layout()->addFooterJs('
        <script>
        $(document).ready(function() {
            $(\'.share-button\').on(\'click\', function (event) {
                event.preventDefault();
                var popup = $(this).find(\'.share-popup-wrapper\');
                var screen = $(this).find(\'.share-popup-screen\');

                if (typeof popup !== \'undefined\' && typeof screen !== \'undefined\') {
                    screen.show();
                    popup.show();
                }

                $(document).on(\'keydown\', function (event) {
                    if (screen.is(\':visible\') && event.keyCode === 27) {
                        screen.hide();
                        popup.hide();
                    }
                });

                $(document).on(\'click\', \'.share-popup-screen\', function (event) {
                    event.preventDefault();
                    screen.hide();
                    popup.hide();
                });
            });

            $(\'.share-link\').on(\'click\', function(event) {
                event.preventDefault();
                window.open($(this).attr(\'href\'), \'\', \'height=450, width=550, top=\' + ($(window).height() / 2 - 275) + \', left=\' + ($(window).width() / 2 - 225) + \', toolbar=0, location=0, menubar=0, directories=0\');

                return false;
            });
        });
        </script>');
?>
