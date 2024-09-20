<?php
/**
 * Banner Rendering
 */
class Banner
{
    /**
     * Banner Fields
     * @var array
     */
    private static $fields = [];

    /**
     * Options
     * @var array
     */
    private static $options = [];

    /**
     * Field Keys
     * @var array
     */
    private static $keys = [];

    /**
     * Render Banner
     * @param  array $args Rendering options
     * @return void
     */
    public static function render($args = [])
    {
        global $post;

        self::$options = array_merge([
            'file' => 'banner_default',
            'group' => 'group_57222a6aad633',
            'post_id' => false,
            'type' => get_post_type(),
        ], $args);

        // Grab Field Keys
        self::$keys = self::getKeys(self::$options['group']);

        // Fill fields with keys
        self::$fields = array_fill_keys(self::$keys, false);

        // Only extend default banner
        if (self::$options['group'] === 'group_57222a6aad633') {
            self::fetchPreset();
        }

        // Current Banner
        if (!is_search()) {
            self::extendFields(get_fields(self::$options['post_id']));
        }

        // Load Banner
        self::fetch();
    }

    /**
     * Get Banner Keys
     * @param  string $group ACF Field Group
     * @return array         All fields within group
     */
    private static function getKeys($group)
    {
        $fields = acf_get_fields($group);

        // No Fields
        if (!$fields) {
            return;
        }

        $names = array_map(function ($field) {
            return $field['name'];
        }, $fields);

        $names = array_filter($names);

        return $names;
    }

    /**
     * Fetch Banner
     * @return void
     */
    private static function fetch()
    {
        Field::setData(self::replaceMergeTags());

        $non_class_specific_keys = array_filter(self::$keys, function ($value, $key) {
            return substr($value, 0, 6) !== 'class_';
        }, ARRAY_FILTER_USE_BOTH);

        if (Field::anyExist(...$non_class_specific_keys)) {
            // Treat banner as a block
            $block = new Block(self::$fields);

            // Grab field specific classes
            Layout::addClasses($block->getFieldClasses());

            get_template_part('sections/' . self::$options['file']);
        }
    }

    /**
     * Trim out empty fields
     * @param  array $fields New Fields
     * @return void
     */
    private static function extendFields($fields = [])
    {
        $keys = self::$keys;

        if (!$fields) {
            return;
        }

        $fields = array_filter($fields, function ($field, $key) use ($keys) {
            return $field && in_array($key, $keys);
        }, ARRAY_FILTER_USE_BOTH);

        self::$fields = array_merge(self::$fields, $fields);
    }

    /**
     * Swap out strings
     * @return array Fields
     */
    private static function replaceMergeTags()
    {
        global $post;

        $matches = array_filter(self::$fields, function ($field) {
            return !is_array($field) && strpos($field, '%') !== false;
        });

        if (empty($matches)) {
            return self::$fields;
        }

        $merge_tags = [
            '%post_title%' => get_the_title(),
            '%search_query%' => get_search_query(),
            '%category%' => is_category() ? single_cat_title('', false) : '',
            '%site_name%' => get_bloginfo('name'),
            '%archive_date%' => single_month_title(' ', false),
        ];

        return array_map(function ($field) use ($merge_tags) {
            return is_array($field) ? $field : strtr($field, $merge_tags);
        }, self::$fields);
    }

    /**
     * Extend preconfigured banners either by user selection or by post type
     */
    private static function fetchPreset()
    {
        // User selected
        if (!is_search() && get_field('opt_banner')) {
            self::extendFields(get_fields(get_field('opt_banner')));
        } else {
            // Assigned by post type
            $preset = new WP_Query([
                'post_type' => 'banner',
                'posts_per_page' => 1,
                'meta_key' => 'banner_post_type_preset',
                'meta_value' => self::$options['type'],
                'meta_compare' => 'LIKE',
            ]);

            if ($preset->have_posts()) {
                foreach ($preset->get_posts() as $post) {
                    self::extendFields(get_fields($post->ID));
                }
            }

            wp_reset_query();
        }
    }
}
