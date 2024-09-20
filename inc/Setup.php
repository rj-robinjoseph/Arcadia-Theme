<?php

namespace Arcadia;

use Arcadia\Dev\Builder;
use Arcadia\Dev\Tracking;
use Arcadia\Theme\Assets;
use Arcadia\Theme\PostTypes;
use Arcadia\Theme\PromoteToWidget;
use Arcadia\Theme\Search;
use Arcadia\Theme\Shortcodes;
use DateTime;
use DateTimeZone;

/**
 * Setup
 */
class Setup
{
    /**
     * Timezone
     * @var string
     */
    private static $timezone = '';

    /**
     * Prepare Theme
     * @return void
     */
    public static function init()
    {
        PromoteToWidget::init();
        Search::init();
        Assets::init();
        Shortcodes::init();

        $timezone = get_option('timezone_string', 'America/Thunder_Bay');

        if (empty($timezone)) {
            $timezone = 'America/Thunder_Bay';
        }

        self::$timezone = new DateTimeZone($timezone);

        spl_autoload_register([__CLASS__, 'resources']);

        if (is_admin()) {
            add_action('wp_dashboard_setup', [__CLASS__, 'dashboardWidgets']);
            add_action('admin_init', [__CLASS__, 'disableEditors']);
            add_action('admin_menu', [__CLASS__, 'addHomePageToMenu']);

            add_filter('mce_buttons_2', [__CLASS__, 'activateStyleFormats']);
            add_filter('acf/fields/flexible_content/layout_title/key=field_57222a09e15e1', [__CLASS__, 'blockTitleVisibilityLabel'], 10, 4);
            add_filter('tiny_mce_before_init', [__CLASS__, 'removeH1FromEditor']);
            add_filter('wp_revisions_to_keep', function ($num, $post) {
                return 30;
            }, 10, 2);

            self::createOptionsPage();
        }

        if (is_admin() && ENV === 'local') {
            Tracking::init();
            Builder::init();
        }

        if (ENV !== 'local') {
            add_filter('acf/settings/show_admin', '__return_false');
        }

        add_action('init', [__CLASS__, 'removeFeatures']);
        add_action('admin_bar_menu', [__CLASS__, 'addThemeSettingsToAdminMenu'], 2000);
        add_action('acf/init', [__CLASS__, 'setupGoogleMapsAPIKey']);

        PostTypes::init();

        add_filter('login_headerurl', [__CLASS__, 'loginUrl']);
        add_filter('xmlrpc_enabled', '__return_false', PHP_INT_MAX);
        add_filter('xmlrpc_methods', '__return_empty_array', PHP_INT_MAX);
        add_filter('xmlrpc_element_limit', function (): int {
            return 1;
        }, PHP_INT_MAX);
    }

    /**
     * PSR-4 Autoloader
     * @param  string  $class Which class to load
     * @return boolean
     */
    public static function resources($className)
    {
        $directories = [
            BASE . '/inc/',
        ];

        foreach ($directories as $directory) {
            if (file_exists($directory . $className . '.php')) {
                require($directory . $className . '.php');
                return true;
            }
        }

        return false;
    }

    /**
     * Remove unused functionality
     * @return void
     */
    public static function removeFeatures()
    {
        // App functionality
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');

        // Remove Emoji Support
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');

        // Disable Tags
        unregister_taxonomy_for_object_type('post_tag', 'post');

        // WordPress Generator
        remove_action('wp_head', 'wp_generator');
    }

    /**
     * Clean up Dashboard widgets
     * @return void
     */
    public static function dashboardWidgets()
    {
        remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
        remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
        remove_meta_box('dashboard_primary', 'dashboard', 'side');
        remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
        remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'normal');

        add_meta_box('shout_support_dashboard', 'Support', [__CLASS__, 'supportWidget'], 'dashboard', 'side', 'high');
    }

    /**
     * Display Support Widget
     * @return void
     */
    public static function supportWidget()
    {
        echo 'Phone: <strong>(807) 285-3404</strong><br>
        Email: <strong><a href="mailto:web@shout-media.ca">web@shout-media.ca</a></strong>';
    }

    /**
     * Theme Options
     * @return void
     */
    public static function createOptionsPage()
    {
        if (function_exists('acf_add_options_page')) {
            $parent = acf_add_options_page([
                'page_title' => 'Theme Settings',
                'menu_title' => 'Theme Settings',
                'redirect' => false,
                'autoload' => true,
                'position' => 58,
            ]);
        }
    }

    public static function addThemeSettingsToAdminMenu()
    {
        global $wp_admin_bar;

        $wp_admin_bar->add_menu([
            'id' => 'theme-settings',
            'title' => __('Theme Settings', DOMAIN),
            'href' => site_url('wp-admin/admin.php?page=acf-options-theme-settings'),
            'meta' => [
                'title' => __('Theme Settings', DOMAIN),
            ],
        ]);
    }

    /**
     * Set Login URL
     * @return string Current Website
     */
    public static function loginUrl()
    {
        return home_url();
    }

    /**
     * Disable Editors
     * @return void
     */
    public static function disableEditors()
    {
        remove_submenu_page('themes.php', 'theme-editor.php');
        remove_submenu_page('plugins.php', 'plugin-editor.php');
    }

    /**
     * Activate style dropdown
     * @param  array $buttons TinyMCE buttons
     * @return array          Updated TinyMCE buttons
     */
    public static function activateStyleFormats($buttons)
    {
        array_unshift($buttons, 'styleselect');

        return $buttons;
    }

    /**
     * Add Google Maps to ACF
     * @return void
     */
    public static function setupGoogleMapsAPIKey()
    {
        acf_update_setting('google_api_key', GOOGLEAPI);
    }

    /**
     * Display visibility alongside block name
     * @param  string  $title  The layout title text
     * @param  array   $field  The flexible content field settings
     * @param  array   $layout The current layout settings
     * @param  integer $index  The current layout index
     * @return string          Extended layout title
     */
    public static function blockTitleVisibilityLabel($title, $field, $layout, $index)
    {
        $visibility = get_sub_field('visibility');
        $now = new DateTime('now', self::$timezone);

        $formatted = '<span class="block-title" data-title="' . $title . '">';

        if ($layout['name'] === 'widget') {
            $formatted .= $layout['sub_fields'][0]['choices'][get_sub_field('widget')] . ' (' . $title . ')';
        } elseif (get_sub_field('friendly_title')) {
            $formatted .= get_sub_field('friendly_title') . ' (' . $title . ')';
        } else {
            $formatted .= $title;
        }

        $formatted .= '</span>';
        $formatted .= '<span class="block-visibility';

        switch ($visibility) {
            case 'disable':
                $formatted .= ' disabled"> - <em>Disabled</em>';
                break;
            case 'schedule':
                $start = new DateTime(get_sub_field('visible_from'), self::$timezone);
                $end = new DateTime(get_sub_field('visible_until'), self::$timezone);
                $format = 'F j @ g:i A';

                if (get_sub_field('visible_until') && $end < $now) {
                    $formatted .= ' disabled"> - <em>Disabled ' . $end->format($format) . '</em>';
                } elseif (get_sub_field('visible_from') && $start > $now) {
                    $formatted .= ' scheduled"> - <em>Scheduled for ' . $start->format($format) . '</em>';
                } elseif (get_sub_field('visible_until')) {
                    $formatted .= ' scheduled"> - <em>Scheduled until ' . $end->format($format) . '</em>';
                } else {
                    $formatted .= '">';
                }

                break;
            default:
                $formatted .= '">';
                break;
        }

        $formatted .= '</span>';

        return $formatted;
    }

    /**
     * Remove H1 from the TinyMCE editor
     * @param  array $init Options
     * @return array       Options
     */
    public static function removeH1FromEditor($init)
    {
        $formats = [
            'Paragraph=p',
            'Heading 2=h2',
            'Heading 3=h3',
            'Heading 4=h4',
            'Heading 5=h5',
            'Heading 6=h6',
            'Address=address',
            'Pre=pre'
        ];

        $init['block_formats'] = implode(";", $formats);

        return $init;
    }

    public static function addHomePageToMenu()
    {
        $home = get_option('page_on_front');
        $url = 'post.php?post=' . $home . '&action=edit';

        if (empty($home)) {
            return;
        }

        add_menu_page('Edit Home', 'Edit Home', 'manage_options', $url, '', 'dashicons-home', 3);
    }
}
