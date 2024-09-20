<?php

namespace Arcadia\Theme;

/**
 * Theme Assets
 */
class Assets
{
    /**
     * List of scripts to defer
     * @var array
     */
    public static $defer = [
        DOMAIN . '-bugherd',
        DOMAIN . '-credit',
    ];

    /**
     * Assign theme assets
     * @return void
     */
    public static function init()
    {
        // Login Logo
        add_action('login_enqueue_scripts', [__CLASS__, 'loginScreenLogo']);

        if (is_admin()) {
            // Admin Stylesheets/JavaScript
            add_action('admin_enqueue_scripts', [__CLASS__, 'adminAssets'], 999);
            add_action('admin_enqueue_scripts', [__CLASS__, 'hideUpdates'], 999);

            // Editor Styles
            add_editor_style('editor.css');
        }

        if (!is_admin()) {
            // Remove Query String
            add_filter('script_loader_src', [__CLASS__, 'removeVersionQueryString'], 15, 1);
            add_filter('style_loader_src', [__CLASS__, 'removeVersionQueryString'], 15, 1);

            // Theme Specific Functionality
            add_action('wp_enqueue_scripts', [__CLASS__, 'scriptsStyles']);

            // Defer loading
            add_filter('script_loader_tag', [__CLASS__, 'addDeferAttribute'], 10, 2);
        }

        add_filter('body_class', [__CLASS__, 'enableAnimation'], 10, 1);
        add_filter('body_class', [__CLASS__, 'displayAudits'], 10, 1);
    }

    /**
     * Add a class to the body to trigger animation
     * @param  array $classes
     * @return array
     */
    public static function enableAnimation($classes)
    {
        if (get_field('enable_animation', 'option')) {
            $classes[] = 'animate-site';
        }

        return $classes;
    }

    /**
     * Add a class to the body to display audits
     * @param  array $classes
     * @return array
     */
    public static function displayAudits($classes)
    {
        if (get_field('audit', 'option') || is_null(get_field('audit', 'option'))) {
            $classes[] = 'show-accessibility';
        }

        if (get_field('breakpoints', 'option') || is_null(get_field('breakpoints', 'option'))) {
            $classes[] = 'show-breakpoints';
        }

        return $classes;
    }

    /**
     * Replace logo on login
     * @return void
     */
    public static function loginScreenLogo()
    {
        if (file_exists(get_template_directory() . '/logo.png')) {
            echo '<style type="text/css">
                #login h1 a,
                .login h1 a {
                    background-image: url(' . get_template_directory_uri() . '/logo.png);
                    background-size: contain;
                    width: 100%;
                    height: 100px;
                }
            </style>';
        }
    }

    public static function hideUpdates()
    {
        $current_user = wp_get_current_user();

        if ($current_user->user_login !== 'shoutmedia') {
            echo '<style type="text/css">
                /* Hide plugin update badge */
                .menu-top .update-plugins,
                .plugin-update-tr,
                #wp-admin-bar-updates,
                a[href="update-core.php"] {
                    display: none !important;
                }

                .plugins .update th,
                .plugins .update td,
                .plugins .active.update th,
                .plugins .active.update td {
                    box-shadow: inset 0 -1px 0 rgba(0,0,0,.1);
                }
            </style>';
        }
    }

    /**
     * Admin specific asset overrides
     * @return void
     */
    public static function adminAssets()
    {
        wp_enqueue_style('admin_css_custom', get_template_directory_uri() . '/admin.css', false, '1.0.0');
        wp_enqueue_script('admin_js_custom', get_template_directory_uri() . '/js/admin-scripts.js', false, '1.0.0');
    }

    /**
     * Remove the ver query string from all resources
     * @param  string $src Resource
     * @return string
     */
    public static function removeVersionQueryString($src)
    {
        if (strpos($src, '?ver') !== false) {
            $rqs = explode('?ver', $src);
            return $rqs[0];
        }

        if (strpos($src, '&ver') !== false) {
            $rqs = explode('&ver', $src);
            return $rqs[0];
        }

        return $src;
    }

    /**
     * Scripts + Styles
     * @return void
     */
    public static function scriptsStyles()
    {
        // Bugherd
        if (get_field('bugherd_id', 'option') && ENV !== 'production') {
            wp_enqueue_script(DOMAIN . '-bugherd', 'https://www.bugherd.com/sidebarv2.js?apikey=' . get_field('bugherd_id', 'option'), [], null, true);
        }

        // Site Credit
        if (get_field('site_credit', 'option')) {
            wp_enqueue_script(DOMAIN . '-credit', 'https://dev.sm-cdn.com/sitecredit/credit.js', [], null, true);
        }

        // Audits
        if (get_field('audit', 'option') || get_field('breakpoints', 'option') || is_null(get_field('audit', 'option')) || is_null(get_field('breakpoints', 'option'))) {
            wp_enqueue_style(DOMAIN . '-audit', get_template_directory_uri() . '/audit.css');
        }
    }

    /**
     * Add a defer attribute to script tags
     * @param  string $tag    Raw script tag
     * @param  string $handle Internal reference
     * @return string         Formatted tag
     */
    public static function addDeferAttribute($tag, $handle)
    {
        if (in_array($handle, self::$defer)) {
            return str_replace(' src', ' defer src', $tag);
        }

        return $tag;
    }

    public static function analyticsTrackingCode()
    {
        $trackingId = get_field('google_analytics_tracking_id', 'option');

        if ($trackingId) {
            echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . $trackingId . '"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag("js", new Date());
              gtag("config", "' . $trackingId . '");
            </script>' . PHP_EOL;
        }
    }
}
