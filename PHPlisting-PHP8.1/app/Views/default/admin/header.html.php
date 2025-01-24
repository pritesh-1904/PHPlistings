<!doctype html>
<html lang="<?php echo locale()->getLocale(); ?>" dir="<?php echo locale()->getDirection(); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title><?php echo layout()->getTitle(); ?></title>
        <link rel="stylesheet" href="<?php echo asset('js/bootstrap/' . locale()->getDirection() . '/bootstrap.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo asset('js/fontawesome/css/all.css'); ?>">
        <?php echo layout()->getCss(); ?>
        <link rel="stylesheet" href="<?php echo asset('css/default/admin/style.css?v=108'); ?>">
        <script src="<?php echo asset('js/jquery/jquery.min.js?v=371'); ?>"></script>
        <?php echo layout()->getJs(); ?>
    </head>
    <body class="default-theme <?php echo locale()->getDirection(); ?>">
