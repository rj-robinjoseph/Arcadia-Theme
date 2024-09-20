<?php

namespace Arcadia\Dev;

use Arcadia\Dev\Generator;

/**
 * Block code generator
 */
class Builder
{
    /**
     * Tracked Groups
     * @var array
     */
    private static $group_names = [
        'Content',
        'Components',
    ];

    /**
     * Process request
     */
    public static function init()
    {
        add_action('save_post', [__CLASS__, 'detectType'], 50, 2);
    }

    /**
     * Create blocks as required
     * @param  integer $post_id Post ID
     * @param  object  $post    Post details
     * @return integer          Post ID
     */
    public static function detectType($post_id, $post)
    {
        if (($post->post_type !== 'acf-field-group' && !in_array($post->post_title, self::$group_names))
            || wp_is_post_revision($post_id)
            || $post->post_status == 'auto-draft') {
            return $post_id;
        }

        switch ($post->post_title) {
            case 'Content':
                self::generateBlocks();
                break;
            case 'Components':
                self::generateComponents();
                break;
        }

        return $post_id;
    }

    /**
     * Create blocks
     */
    private static function generateBlocks()
    {
        $group = acf_get_fields('group_572229fc5045c');
        $blocks = self::findMissingLayouts($group, 'blocks');

        array_walk($blocks, function ($block, $key) {
            self::runGenerator($block['name'], $block['sub_fields'], 'block');
            self::createBlockStylesheet($block['name']);
            self::appendBlockPartial($block['name']);
        });
    }

    /**
     * Create components
     */
    private static function generateComponents()
    {
        $group = acf_get_fields('group_5c903f684a8ae');
        $components = self::findMissingLayouts($group, 'components');

        array_walk($components, function ($component, $key) {
            self::runGenerator($component['name'], $component['sub_fields'], 'component');
            self::createComponentStylesheet($component['name']);
            self::injectComponentPartial($component['name']);
        });
    }

    /**
     * Create a code block
     * @param  string $name  Block name
     * @param  array  $fields fields
     * @param  string $type Block or Component
     * @return void
     */
    private static function runGenerator($name, $fields, $type)
    {
        if ($fields[0]['type'] === 'clone') {
            $fields = acf_get_fields($fields[0]['clone'][0]);
        }

        $generator = new Generator($fields);
        $generator->setName(self::convertBlockName($name));
        $generator->setType($type);

        file_put_contents(BASE . '/' . $type . 's/' . $name . '.php', $generator->output());
    }

    /**
     * Generate a list of missing blocks
     * @param  array  $data   ACF flexible field
     * @param  string $folder Block path
     * @return array          Missing blocks
     */
    private static function findMissingLayouts($data, $folder)
    {
        if (!$data) {
            return [];
        }

        $blocks = array_filter($data[0]['layouts'], function ($item) use ($folder) {
            return !file_exists(BASE . '/' . $folder . '/' . $item['name'] . '.php');
        });

        if (empty($blocks)) {
            return [];
        }

        return $blocks;
    }

    /**
     * Clone default block partial
     * @param  string $block Block name
     * @return void
     */
    private static function createBlockStylesheet($block)
    {
        $template  = '.' . self::convertBlockName($block) . ' {' . PHP_EOL;
        $template .= '    //' . PHP_EOL;
        $template .= '}';

        file_put_contents(BASE . '/src/scss/blocks/_' . $block . '.scss', $template);
    }

    /**
     * Create component partial
     * @param  string $block  Block name
     * @return void
     */
    private static function createComponentStylesheet($block)
    {
        $template  = '.' . self::convertBlockName($block) . ' {' . PHP_EOL;
        $template .= '    //' . PHP_EOL;
        $template .= '}';

        file_put_contents(BASE . '/src/scss/components/_' . $block . '.scss', $template);
    }

    /**
     * Append partial to end of stylesheet
     * @param  string $block Block name
     * @return void
     */
    private static function appendBlockPartial($block)
    {
        file_put_contents(BASE . '/src/scss/style.scss', '@import "blocks/' . $block . '";' . PHP_EOL, FILE_APPEND);
    }

    /**
     * Append partial to end of stylesheet
     * @param  string $block  Block name
     * @return void
     */
    private static function injectComponentPartial($block)
    {
        $import = '@import "components/' . $block . '";';

        $content = file_get_contents(BASE . '/src/scss/style.scss');
        $content = str_replace('/* components */', $import . PHP_EOL . '/* components */', $content);

        file_put_contents(BASE . '/src/scss/style.scss', $content);
    }

    /**
     * Convert underscores to hyphens
     * @param  string $name Block name
     * @return string       Block name
     */
    private static function convertBlockName($name)
    {
        return str_replace("_", "-", $name);
    }
}
