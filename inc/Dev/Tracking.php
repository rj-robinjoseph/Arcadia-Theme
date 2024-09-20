<?php

namespace Arcadia\Dev;

/**
 * Update internal timestamp
 */
class Tracking
{
    /**
     * Process request
     * @return void
     */
    public static function init()
    {
        add_action('save_post', [__CLASS__, 'trackUpdate'], 999, 2);
    }

    /**
     * Add timestamp to update file
     * @return mixed
     */
    public static function trackUpdate($post_id, $post)
    {
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            || $post->post_status == 'auto-draft'
            || wp_is_post_revision($post_id)) {
            return $post_id;
        }

        $exceptions = [
            'attachment',
            'acf-field-group',
        ];

        $post_type = get_post_type($post_id);

        if (in_array($post_type, $exceptions) || wp_is_post_autosave($post_id)) {
            return $post_id;
        }

        file_put_contents(BASE . '/updated.txt', time());
    }
}
