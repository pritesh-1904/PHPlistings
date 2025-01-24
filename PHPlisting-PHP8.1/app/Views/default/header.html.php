<!doctype html>
<html lang="<?php echo locale()->getLocale(); ?>" dir="<?php echo locale()->getDirection(); ?>">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo layout()->getTitle(); ?></title>
        <?php foreach (layout()->getMeta() as $meta) { ?>
            <?php if ($meta->content != '') { ?>
                <meta <?php echo e($meta->type); ?>="<?php echo e($meta->name); ?>" content="<?php echo e($meta->content); ?>" />
            <?php } ?>
        <?php } ?>
        <link rel="canonical" href="<?php echo layout()->getCanonicalUrl(); ?>" />
        <?php if (count(locale()->getSupported()) > 1) { ?>
            <?php foreach (locale()->getSupported() as $locale) { ?>
                <?php if (locale()->isDefault($locale)) { ?>
                    <link rel="alternate" hreflang="x-default" href="<?php echo route(getRoute(), request()->get->all(), $locale); ?>" />
                <?php } ?>
                <link rel="alternate" hreflang="<?php echo $locale; ?>" href="<?php echo route(getRoute(), request()->get->all(), $locale); ?>" />
            <?php } ?>
        <?php } ?>
        <link rel="stylesheet" href="<?php echo asset('js/bootstrap/' . locale()->getDirection() . '/bootstrap.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo asset('js/fontawesome/css/all.css'); ?>">
        <?php echo layout()->getCss(); ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&family=Open+Sans:wght@300;400;600;700;800;900&family=Ubuntu:wght@300;400;600;700;800;900&family=Quicksand:wght@300;400;600;700;800;900&family=Inter:wght@300;400;600;700;800;900&display=swap">
        <link rel="stylesheet" href="<?php echo theme()->getCssUrl(); ?>">
        <script src="<?php echo asset('js/jquery/jquery.min.js?v=371'); ?>"></script>
        <?php echo layout()->getJs(); ?>
    </head>
    <body class="<?php echo locale()->getDirection(); ?>">
