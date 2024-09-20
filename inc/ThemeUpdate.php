<?php

namespace Arcadia;

class ThemeUpdate
{
    public static $url = 'https://shouthub.ca/wp-update';
    public static $slug;

    public static function check()
    {
        self::$slug = basename(BASE);

        add_filter('pre_set_site_transient_update_themes', [__CLASS__, 'checkForUpdate']);
    }

    public static function checkForUpdate($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $request = self::prepareRequest('theme_update', [
            'slug' => self::$slug,
            'version' => $transient->checked[self::$slug],
        ]);

        $raw_response = wp_remote_post(self::$url, $request);

        $response = null;

        if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)) {
            $response = unserialize($raw_response['body']);
        }

        if (!empty($response)) {
            $transient->response[self::$slug] = $response;
        }

        return $transient;
    }

    public static function prepareRequest($action, $args)
    {
        global $wp_version;

        return [
            'body' => [
                'action' => $action,
                'request' => serialize($args),
                'api-key' => md5(home_url()),
                'domain' => $_SERVER['HTTP_HOST'],
                'version' => $wp_version,
            ],
            'user-agent' => 'WordPress/'. $wp_version .'; '. home_url(),
        ];
    }
}
