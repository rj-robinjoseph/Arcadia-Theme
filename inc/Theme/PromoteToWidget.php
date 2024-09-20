<?php

namespace Arcadia\Theme;

/**
 * Promote blocks to widget post type
 */
class PromoteToWidget
{
    /**
     * Set hooks for promotion
     * @return void
     */
    public static function init()
    {
        add_action('wp_ajax_promote_to_widget', [__CLASS__, 'promote']);
    }

    /**
     * Perform promotion
     * @return void
     */
    public static function promote()
    {
        $key = array_keys($_POST['acf']['field_57222a09e15e1']);
        $data = self::flatten($_POST['acf']['field_57222a09e15e1'][$key[0]]);

        $field_data = [
            'blocks' => [
                0 => $data,
            ],
        ];

        $post_id = wp_insert_post([
            'post_title' => wp_strip_all_tags($_POST['title']),
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'widget',
        ]);

        update_field('field_58082852b7386', $field_data, $post_id);
        update_field('field_5806e99932cb3', 'block', $post_id);

        echo json_encode([
            'status' => 'success',
            'post_id' => $post_id,
        ]);

        wp_die();
    }

    /**
     * Condense array to just label and values
     * @param  array $array
     * @return array
     */
    private static function flatten($array)
    {
        $data = [];

        foreach ($array as $key => $value) {
            if ($key == 'acf_fc_layout') {
                $data[$key] = $value['value'];
            } elseif (is_array($value) && count($value) === 1) {
                $keys = array_keys($value);
                $data[$value[$keys[0]]['parent']] = [$value[$keys[0]]['label'] => self::trim($value[$keys[0]])];
            } elseif (is_array($value) && count($value) > 2) {
                $data[$value['label']] = self::trim($value);
            } else {
                $data[$value['label']] = $value['value'];
            }
        }

        return $data;
    }

    /**
     * Remove clone blocks
     * @param  array $array
     * @return array
     */
    private static function trim($array)
    {
        $data = [];
        $run = count($array) - 2;

        if (array_key_exists('acfcloneindex', $array)) {
            $run -= 1;
        }

        if (array_key_exists('parent', $array)) {
            $run -= 1;
        }

        for ($i = 0; $i < $run; $i++) {
            $data[$i] = self::flatten($array[$i]);
        }

        return $data;
    }
}
