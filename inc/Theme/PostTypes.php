<?php

namespace Arcadia\Theme;

use WP_Query;

/**
 * Custom post type interactions
 */
class PostTypes
{
    /**
     * Post Type
     * @var string
     */
    private static $post_type = null;

    /**
     * Additional Banner Options
     * @var array
     */
    private static $banner_options = [
        'results' => 'Search Results',
        'blog' => 'Blog',
        'archives' => 'Archives',
        'not_found' => '404 Page',
    ];

    /**
     * Additional Layout Options
     * @var array
     */
    private static $layout_options = [
        'results' => 'Search Results',
        'blog' => 'Blog',
        'archives' => 'Archives',
        'not_found' => '404 Page',
    ];

    /**
     * Setup theme post types
     * @return void
     */
    public static function init()
    {
        // Setup Post Types
        add_action('init', [__CLASS__, 'createPostTypes']);

        // Add Post Types to Layout Override
        add_filter('acf/load_field/name=layout_post_type_preset', [__CLASS__, 'acfLoadLayoutField']);

        // Add Post Types to Banner Override
        add_filter('acf/load_field/name=banner_post_type_preset', [__CLASS__, 'acfLoadBannerField']);

        // Display Posts assigned to Widgets
        add_filter('acf/load_field/key=field_594883992f7ec', [__CLASS__, 'acfLoadPageList']);

        // Widgets - Blocks
        add_filter('acf/load_field/key=field_582531244a4aa', [__CLASS__, 'acfLoadWidgetBlocks']);

        // Widgets - Components
        add_filter('acf/load_field/key=field_5ca17138f8cb4', [__CLASS__, 'acfLoadWidgetComponent']);

        // Add Columns
        add_filter('manage_banner_posts_columns', [__CLASS__, 'columnNames']);
        add_filter('manage_layout_posts_columns', [__CLASS__, 'columnNames']);
        add_filter('manage_widget_posts_columns', [__CLASS__, 'widgetColumnNames']);

        // Populate Columns
        add_action('manage_banner_posts_custom_column', [__CLASS__, 'bannerColumnValues'], 10, 2);
        add_action('manage_layout_posts_custom_column', [__CLASS__, 'layoutColumnValues'], 10, 2);
        add_action('manage_widget_posts_custom_column', [__CLASS__, 'widgetColumnValues'], 10, 2);

        // Sortable Columns
        add_filter('manage_edit-banner_sortable_columns', [__CLASS__, 'columnSortable']);
        add_filter('manage_edit-layout_sortable_columns', [__CLASS__, 'columnSortable']);
        add_filter('manage_edit-widget_sortable_columns', [__CLASS__, 'widgetColumnSortable']);

        // Column Sorting
        add_action('pre_get_posts', [__CLASS__, 'bannerColumnSort'], 1);
        add_action('pre_get_posts', [__CLASS__, 'layoutColumnSort'], 1);
        add_action('pre_get_posts', [__CLASS__, 'widgetColumnSort'], 1);

        // Preset Warning Messages
        add_filter('acf/prepare_field/key=field_57e9da5c492e8', [__CLASS__, 'presetWarning']);
        add_filter('acf/prepare_field/key=field_57293e2b4fc99', [__CLASS__, 'presetWarning']);
    }

    /**
     * Create Post Types
     * @return void
     */
    public static function createPostTypes()
    {
        self::genericPostType('banner', 'Banner', 'Banners', ['menu_position' => 56]);
        self::genericPostType('layout', 'Layout', 'Layouts', ['menu_position' => 57]);
        self::genericPostType('widget', 'Widget', 'Widgets', ['menu_position' => 57]);
    }

    /**
     * Basic post type definition
     * @param  string  $type     Post type slug
     * @param  string  $name     Post type name
     * @param  string  $plural   Plural of post type name
     * @param  array   $options  Extend base post type options
     * @return void
     */
    public static function genericPostType($type, $name, $plural, $options = [])
    {
        register_post_type($type, array_merge([
            'labels' => [
                'name' => __($plural, DOMAIN),
                'singular_name' => __($name, DOMAIN),
                'add_new_item' => __('Add New ' . $name, DOMAIN),
                'edit_item' => __('Edit ' . $name, DOMAIN),
                'new_item' => __('New ' . $name, DOMAIN),
                'view_item' => __('View ' . $name, DOMAIN),
                'search_items' => __('Search ' . $plural, DOMAIN),
                'not_found' => __('No ' . $plural . ' found', DOMAIN),
                'not_found_in_trash' => __('No ' . $plural . ' found in trash', DOMAIN),
            ],
            'menu_position' => 56,
            'public' => true,
            'supports' => [
                'title',
                'revisions',
            ],
            'exclude_from_search' => true,
            'capability_type' => 'post',
        ], $options));
    }

    /**
     * Add columns to widget index
     * @param  array $columns Existing columns
     * @return array          Updated columns
     */
    public static function widgetColumnNames($columns)
    {
        unset($columns['date']);

        $columns['type'] = __('Type', DOMAIN);
        $columns['date'] = __('Date', DOMAIN);

        return $columns;
    }

    /**
     * Populate new widget columns
     * @param  string $column  Column index
     * @param  string $post_id Post ID
     * @return string          Column value
     */
    public static function widgetColumnValues($column, $post_id)
    {
        switch ($column) {
            case 'type':
                echo ucwords(get_field('type'));
                break;
        }
    }

    /**
     * Register new widget columns as sortable
     * @param  array $columns Existing columns
     * @return array          Updated columns
     */
    public static function widgetColumnSortable($columns)
    {
        $columns['type'] = 'type';

        return $columns;
    }

    /**
     * Add sorting to main query
     * @param  object $query Existing query
     * @return void
     */
    public static function widgetColumnSort($query)
    {
        if ($query->is_main_query() && ($orderby = $query->get('orderby'))) {
            switch ($orderby) {
                case 'type':
                    $query->set('meta_key', 'type');
                    $query->set('orderby', 'meta_value');
                    break;
            }
        }
    }

    /**
     * Add columns to widget index
     * @param  array $columns Existing columns
     * @return array          Updated columns
     */
    public static function columnNames($columns)
    {
        unset($columns['date']);

        $columns['preset'] = __('Presets', DOMAIN);
        $columns['date'] = __('Date', DOMAIN);

        return $columns;
    }

    /**
     * Register new widget columns as sortable
     * @param  array $columns Existing columns
     * @return array          Updated columns
     */
    public static function columnSortable($columns)
    {
        $columns['preset'] = 'preset';

        return $columns;
    }

    /**
     * Populate new widget columns
     * @param  string $column  Column index
     * @param  string $post_id Post ID
     * @return void
     */
    public static function layoutColumnValues($column, $post_id)
    {
        if ($column !== 'preset') {
            return;
        }

        $labels = get_field('layout_post_type_preset');

        if (empty($labels)) {
            echo '';
            return;
        }

        $choices = array_merge(self::getPostTypes(), self::$layout_options);

        $labels = array_map(function ($post) use ($choices) {
            return $choices[$post];
        }, $labels);

        echo implode(', ', $labels);
    }

    /**
     * Add sorting to main query
     * @param  object $query Existing query
     * @return void
     */
    public static function layoutColumnSort($query)
    {
        if ($query->is_main_query() && ($orderby = $query->get('orderby'))) {
            switch ($orderby) {
                case 'preset':
                    $query->set('meta_key', 'layout_post_type_preset');
                    $query->set('orderby', 'meta_value');
                    break;
            }
        }
    }

    /**
     * Populate new widget columns
     * @param  string $column  Column index
     * @param  string $post_id Post ID
     * @return void
     */
    public static function bannerColumnValues($column, $post_id)
    {
        if ($column !== 'preset') {
            return;
        }

        $labels = get_field('banner_post_type_preset');

        if (empty($labels)) {
            echo '';
            return;
        }

        $choices = array_merge(self::getPostTypes(), self::$banner_options);

        $labels = array_map(function ($post) use ($choices) {
            return $choices[$post];
        }, $labels);

        echo implode(', ', $labels);
    }

    /**
     * Add sorting to main query
     * @param  object $query Existing query
     * @return void
     */
    public static function bannerColumnSort($query)
    {
        if ($query->is_main_query() && ($orderby = $query->get('orderby'))) {
            switch ($orderby) {
                case 'preset':
                    $query->set('meta_key', 'banner_post_type_preset');
                    $query->set('orderby', 'meta_value');
                    break;
            }
        }
    }

    /**
     * Display warning for presets
     * @param  array $field Existing field
     * @return array        Updated field
     */
    public static function presetWarning($field)
    {
        // Store current post type
        if (is_null(self::$post_type)) {
            self::$post_type = get_post_type();
        }

        // Ignore field group
        if (self::$post_type == 'acf-field-group') {
            return $field;
        }

        $fields = [
            'banner' => 'field_57e9da5c492e8',
            'layout' => 'field_57293e2b4fc99',
        ];

        if (in_array($field['key'], $fields)) {
            $type = array_keys($fields, $field['key']);

            $preset = get_posts([
                'post_type' => $type[0],
                'posts_per_page' => 1,
                'meta_key' => $type[0] . '_post_type_preset',
                'meta_value' => self::$post_type,
                'meta_compare' => 'LIKE',
            ]);

            if (!empty($preset)) {
                $field['label'] .= '<span class="field-assigned">Assigned: ' . $preset[0]->post_title . '</span>';
            }

            wp_reset_query();
        }

        return $field;
    }

    /**
     * Display Posts Types in a drop down
     * @param  array $field ACF Field to modify
     * @return array        Field
     */
    public static function acfLoadLayoutField($field)
    {
        $field['choices'] = array_merge(self::getPostTypes(), self::$layout_options);

        return $field;
    }

    /**
     * Display Posts Types in a drop down
     * @param  array $field ACF Field to modify
     * @return array        Field
     */
    public static function acfLoadBannerField($field)
    {
        $field['choices'] = array_merge(self::getPostTypes(), self::$banner_options);

        return $field;
    }

    /**
     * Display block level widgets in a drop down
     * @param  array $field ACF Field to modify
     * @return array        Field
     */
    public static function acfLoadWidgetBlocks($field)
    {
        $field['choices'] = self::getWidgetsByType('block');

        return $field;
    }

    /**
     * Display block level widgets in a drop down
     * @param  array $field ACF Field to modify
     * @return array        Field
     */
    public static function acfLoadWidgetComponent($field)
    {
        $field['choices'] = self::getWidgetsByType('component');

        return $field;
    }

    /**
     * Display Posts Types in a drop down
     * @param  array $field ACF Field to modify
     * @return array        Field
     */
    public static function acfLoadPageList($field)
    {
        global $wpdb;

        $assignments = $wpdb->get_results("SELECT * FROM `wp_postmeta` AS `meta` INNER JOIN `wp_posts` AS `post` ON `meta`.`post_id` = `post`.`ID` WHERE `meta`.`meta_key` LIKE '%widget%' AND `meta`.`meta_value` = '" . get_the_ID() . "' AND `post`.`post_status` = 'publish' AND `post`.`post_Type` != 'revision' ORDER BY `post`.`post_title`");

        $message = '<ul class="page-list">';

        if ($assignments) {
            foreach ($assignments as $assignment) {
                $message .= '<li><a href="post.php?post=' . $assignment->post_id . '&action=edit">' . $assignment->post_title . '</a></li>';
            }
        } else {
            $message .= '<li>Not Assigned</li>';
        }

        $message .= '</ul>';

        $field['message'] = $message;

        return $field;
    }

    /**
     * Get Available Post Types
     * @return array Post Types
     */
    private static function getPostTypes()
    {
        $exceptions = [
            'attachment',
            'layout',
            'banner',
            'widget',
        ];

        // Fetch all post types
        $all_types = get_post_types([
            'public' => true,
            'show_ui' => true,
        ], 'objects');

        // Remove exceptions
        $types = array_filter($all_types, function ($type) use ($exceptions) {
            return !in_array($type, $exceptions);
        }, ARRAY_FILTER_USE_KEY);

        // Flatten to just name
        return array_map(function ($type) {
            return $type->labels->singular_name;
        }, $types);
    }

    /**
     * Get Widgets by type
     * @param  string $type Widget type
     * @return array        Available choices
     */
    private static function getWidgetsByType($type)
    {
        $widgets = new WP_Query([
            'post_type' => 'widget',
            'meta_key' => 'type',
            'meta_value' => $type,
            'posts_per_page' => -1,
        ]);

        $choices = [];

        if ($widgets->have_posts()) {
            foreach ($widgets->get_posts() as $widget) {
                $choices[$widget->ID] = $widget->post_title;
            }
        }

        wp_reset_query();

        return $choices;
    }
}
