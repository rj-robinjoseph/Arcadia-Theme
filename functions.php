<?php

define('DOMAIN', 'arcadia');
define('BASE', dirname(__FILE__));
define('GOOGLEAPI', 'AIzaSyDivzhX9fUCCqvaJWhbVXu-y1EDBCycwuU');
define('ACF_PRO_LICENSE', 'b3JkZXJfaWQ9MzQ4NzV8dHlwZT1kZXZlbG9wZXJ8ZGF0ZT0yMDE0LTA3LTE1IDE5OjU1OjU1');

require_once(BASE . '/inc/helpers.php');
require_once(BASE . '/inc/autoloader.php');

setEnv();

\Arcadia\Setup::init();

/**
 * Current Theme Setup
 */
function theme_setup()
{
    // Thumbnails
    add_theme_support('post-thumbnails');

    // Title Tag
    add_theme_support('title-tag');

    // Menus
    register_nav_menu('primary', __('Navigation Menu', DOMAIN));
    register_nav_menu('secondary', __('Footer Menu', DOMAIN));
    register_nav_menu('mobile', __('Mobile Menu', DOMAIN));

    // When inserting an image don't link it
    update_option('image_default_link_type', 'none');

    // Remove Gallery Styling
    add_filter('use_default_gallery_style', '__return_false');

    // Additional Image Sizes
    add_image_size('banner', 1920, 1920);
}

add_action('after_setup_theme', 'theme_setup');

/**
 * Load theme specific assets
 */
function scripts_styles()
{
    wp_enqueue_style(DOMAIN . '-style', get_stylesheet_uri());

    wp_register_script(DOMAIN . '-gmaps', 'https://maps.googleapis.com/maps/api/js?key=' . GOOGLEAPI, [], null, true);
    wp_register_script(DOMAIN . '-cluster', get_template_directory_uri() . '/js/markerclusterer.js', [], null, true);

    if (Layout::containsBlock('map')) {
        wp_enqueue_script(DOMAIN . '-gmaps');
        wp_enqueue_script(DOMAIN . '-cluster');
    }

    wp_enqueue_script(DOMAIN . '-polyfill', 'https://cdnjs.cloudflare.com/polyfill/v3/polyfill.min.js', [], null);
    wp_enqueue_script(DOMAIN . '-slick', get_template_directory_uri() . '/js/slick.min.js', ['jquery'], null);
    wp_enqueue_script(DOMAIN . '-main', get_template_directory_uri() . '/js/main.js', [], null, true);
}

add_action('wp_enqueue_scripts', 'scripts_styles');

/**
 * Custom Editor Styles
 * @param  array $init TinyMCE Options
 * @return array       Updated TinyMCE Options
 */
function editor_style_options($init)
{
    $style_formats = [
        [
            'title' => 'Small Text',
            'selector' => '*',
            'classes' => 'text-small',
        ],
        [
            'title' => 'Medium Text',
            'selector' => '*',
            'classes' => 'text-medium',
        ],
        [
            'title' => 'Large Text',
            'selector' => '*',
            'classes' => 'text-large',
        ],
        [
            'title' => 'Extra Large Text',
            'selector' => '*',
            'classes' => 'text-xlarge',
        ],
        [
            'title' => 'Button',
            'selector' => 'a',
            'classes' => 'btn',
        ],
    ];

    $init['style_formats'] = json_encode($style_formats);

    return $init;
}

add_filter('tiny_mce_before_init', 'editor_style_options');
