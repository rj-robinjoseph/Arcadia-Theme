<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php

    if (is_search()) {
        echo '<meta name="robots" content="noindex, nofollow">' . PHP_EOL;
    }

    ?>
    <meta name="title" content="<?php wp_title('|', true, 'right'); ?>">
    <meta name="Copyright" content="Copyright <?php bloginfo('name'); ?> <?php echo date('Y'); ?>. All Rights Reserved.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <?php

    if (get_field('mobile_theme_colour', 'option')) {
        echo '<meta name="theme-color" content="' . get_field('mobile_theme_colour', 'option') . '">' . PHP_EOL;
    }

    if (get_field('ios_status_bar', 'option')) {
        echo '<meta name="apple-mobile-web-app-status-bar-style" content="' . get_field('ios_status_bar', 'option') . '">' . PHP_EOL;
    }

    ?>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

    <?php wp_head(); ?>
    <?php Arcadia\Theme\Assets::analyticsTrackingCode(); ?>
</head>

<body <?php body_class(); ?>>

    <a href="#main" class="skip-link"><?php _e('Skip to content', DOMAIN); ?></a>

    <?php get_template_part('sections/header'); ?>
    <?php get_template_part('sections/mobile_nav'); ?>
