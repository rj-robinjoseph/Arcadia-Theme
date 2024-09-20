<?php
/**
 * Layout Builder
 */
class Layout
{
    /**
     * All Blocks
     * @var array
     */
    private static $blocks = [];

    /**
     * Current Page Content
     * @var string
     */
    private static $content = '';

    /**
     * Rendering Options
     * @var array
     */
    private static $options = [];

    /**
     * Current Key
     * @var array
     */
    private static $block_ids = [];

    /**
     * Current Block
     * @var object
     */
    private static $current_block;

    /**
     * Current block classes
     * @var array
     */
    private static $classes = [];

    public static function setup($args = [])
    {
        self::$options = array_merge([
            'default' => 'basic_content',
            'post_id' => false,
            'type' => get_post_type(),
        ], $args);
    }

    /**
     * Render Layout
     * @param  array $args Rendering options
     * @return void
     */
    public static function render($args = [])
    {
        global $post;

        self::setup($args);
        self::$content = get_the_content();
        self::getBlocks();
        self::flexible(self::$blocks);
    }

    /**
     * Store current block within loop
     * @param  object $block
     * @return void
     */
    private static function setCurrentBlock($block)
    {
        self::$current_block = $block;
    }

    /**
     * Disply contents of flexible field
     * @param  array  $blocks Blocks to load
     * @param  string $folder Additional folder depth
     * @return void
     */
    public static function flexible($blocks = [], $folder = 'blocks')
    {
        global $post;

        if (!$blocks) {
            return;
        }

        foreach ($blocks as $fields) {
            Field::setData($fields);

            $block = new Block($fields);
            $block->setId(self::generateBlockID());

            self::$classes = array_merge([], $block->getFieldClasses());
            self::setCurrentBlock($block);

            if ($block->isVisible()) {
                get_template_part($folder . '/' . $block->layout());
            }

            Field::restore();
        }
    }

    /**
     * Generate current block's id
     * @param  string $prefix ID Prefix
     * @return string         ID
     */
    private static function generateBlockID($prefix = 'block')
    {
        $key = Field::get('block_id', $prefix);

        if (array_key_exists($key, self::$block_ids)) {
            self::$block_ids[$key]++;
        } else {
            self::$block_ids[$key] = 1;
        }

        if ($key == $prefix || self::$block_ids[$key] > 1) {
            $key .= self::$block_ids[$key];
        }

        return $key;
    }

    /**
     * Print block ID attribute
     * @param  string $prefix Naming prefix
     * @return void
     */
    public static function id($prefix = null)
    {
        if (!is_null($prefix)) {
            $key = self::generateBlockID($prefix);
        } else {
            $key = self::$current_block->id();
        }

        echo ' id="' . $key . '"';
    }

    /**
     * Load partial view
     * @param  string $view File to load
     * @param  array  $data Supporting data
     * @return void
     */
    public static function partial($view, $data = [])
    {
        global $post;

        Field::extend($data);

        get_template_part('blocks/partials/' . $view);
    }

    /**
     * Load a collection of partial views
     * @param  array $views Views
     * @return void
     */
    public static function partials(...$views)
    {
        foreach ($views as $view) {
            self::partial($view);
        }
    }

    /**
     * Helper for displaying generic page content
     * @return void
     */
    public static function getContent()
    {
        echo apply_filters('the_content', self::$content);
    }

    /**
     * Get and store all blocks
     * @return void
     */
    private static function getBlocks()
    {
        global $post;

        self::$blocks = [];
        self::$block_ids = [];

        $current_blocks = get_field('blocks', self::$options['post_id']);
        $layout_option = get_field('opt_layout', self::$options['post_id']);

        if (is_array($current_blocks)) {
            $first_block = current($current_blocks);

            if (is_null($first_block)) {
                $current_blocks = false;
            }
        }

        if ($layout_option) {
            if (get_field('blocks', $layout_option)) {
                self::$blocks = array_merge(self::$blocks, get_field('blocks', $layout_option));
            }
        } else {
            self::$blocks = array_merge(self::$blocks, self::getBlocksByPostType());
        }

        // No blocks set on current page
        // Display default block
        if (!$current_blocks && !is_null(self::$options['default'])) {
            $current_blocks = [
                ['acf_fc_layout' => self::$options['default']]
            ];
        }

        // No more blocks to be added
        if (empty($current_blocks)) {
            return;
        }

        // Locate page_content placeholder
        $page_content = array_search('page_content', array_column(self::$blocks, 'acf_fc_layout'));

        // Inject page blocks
        if ($page_content !== false) {
            array_splice(self::$blocks, $page_content, 1, $current_blocks);
        } else {
            array_splice(self::$blocks, $page_content, 0, $current_blocks);
        }
    }

    /**
     * Check if block exists
     * @param  string  $block Block Name
     * @return boolean
     */
    public static function containsBlock($block)
    {
        return in_array($block, self::blockNames(self::getVisibleBlocks()));
    }

    /**
     * Check if the layout contains any of the blocks provided
     * @param  array  $blocks List of blocks to test
     * @return boolean
     */
    public static function containsBlocks(...$blocks)
    {
        return count(array_intersect(self::blockNames(self::getVisibleBlocks()), $blocks)) > 0;
    }

    /**
     * Filter out block names
     * @param  array $blocks All block level data
     * @return array         Block names
     */
    private static function blockNames($blocks)
    {
        return array_map(function ($block) {
            return $block['acf_fc_layout'];
        }, $blocks);
    }

    /**
     * Get blocks for a post type preset
     * @return array Available blocks
     */
    private static function getBlocksByPostType()
    {
        $preset_blocks = [];

        $preset = new WP_Query([
            'post_type' => 'layout',
            'posts_per_page' => 1,
            'meta_key' => 'layout_post_type_preset',
            'meta_value' => self::$options['type'],
            'meta_compare' => 'LIKE',
        ]);

        if ($preset->have_posts()) {
            foreach ($preset->get_posts() as $post) {
                $blocks = get_field('blocks', $post->ID);

                if ($blocks) {
                    $preset_blocks = $blocks;
                }
            }
        }

        wp_reset_query();

        return $preset_blocks;
    }

    /**
     * Filter out visible blocks
     * @param  array $blocks All Blocks
     * @return array         Just the visible blocks
     */
    private static function visibleBlocks($blocks)
    {
        return array_filter($blocks, function ($block) {
            $test = new Block($block);
            return $test->isVisible();
        });
    }

    /**
     * Retrive only the visible blocks
     * @return array Blocks
     */
    private static function getVisibleBlocks()
    {
        self::setup();
        self::getBlocks();

        $allblocks = [];

        // Replace widget/combo
        array_walk(self::$blocks, function ($block) use (&$allblocks) {
            $allblocks[] = $block;
            $layout = $block['acf_fc_layout'];

            if ($layout === 'widget' && !empty($block['widget'])) {
                $block = get_field('blocks', $block['widget']);
            }

            if (in_array($layout, ['widget', 'combo'])) {
                $allblocks = array_merge($allblocks, $block['blocks']);
            }
        });

        return self::visibleBlocks($allblocks);
    }

    /**
     * Store an additional class to current block
     * @param  string $class Class name
     * @return void
     */
    public static function addClass($class)
    {
        self::$classes[] = $class;
    }

    /**
     * Add multiple classes to current block
     * @param  array $classes
     * @return void
     */
    public static function addClasses($classes)
    {
        self::$classes = array_merge($classes, self::$classes);
    }

    /**
     * Display list of current classes
     * @param  array $classes Additonal class names
     * @return void
     */
    public static function classes(...$classes)
    {
        self::addClasses($classes);

        echo implode(" ", array_unique(self::$classes));
    }
}
