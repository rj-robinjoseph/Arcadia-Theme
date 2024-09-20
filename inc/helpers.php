<?php

/**
 * Decide on text color based on background
 * @param  string $hexcolor Background Color
 * @param  string $dark     Dark contrast colour
 * @param  string $light    Light contrast colour
 * @return string           Color
 */
function get_contrast($hexcolor, $dark = 'black', $light = 'white')
{
    if (substr($hexcolor, 0, 1) == '#') {
        $hexcolor = substr($hexcolor, 1);
    }

    $r = hexdec(substr($hexcolor, 0, 2));
    $g = hexdec(substr($hexcolor, 2, 2));
    $b = hexdec(substr($hexcolor, 4, 2));

    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

    return ($yiq >= 128) ? $dark : $light;
}

/**
 * Retrieve meta data about attachments
 * @param  integer $attachment_id Attachment ID
 * @param  array   $size          Image Size
 * @return array                  Details
 */
function wp_get_attachment($attachment_id, $size = [])
{
    $src        = wp_get_attachment_image_src($attachment_id, $size);
    $attachment = get_post($attachment_id);

    // Attachment doesn't exist
    if (!$src) {
        return false;
    }

    return [
        'alt'         => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
        'caption'     => $attachment->post_excerpt,
        'description' => $attachment->post_content,
        'href'        => get_permalink($attachment->ID),
        'src'         => $src[0],
        'width'       => $src[1],
        'height'      => $src[2],
        'title'       => $attachment->post_title
    ];
}

/**
 * Make sure HTTP has been added to URL
 * @param  string $url URL
 * @return string      Fixed URL
 */
function check_url($url)
{
    if (substr($url, 0, 1) != '#' && substr($url, 0, 4) != 'http') {
        return 'http://' . $url;
    }

    return $url;
}

/**
 * Get menu name by theme location
 * @param  string $location Location slug
 * @return string           Menu name
 */
function get_menu_name_from_location($location)
{
    $locations = get_nav_menu_locations();
    $menu = wp_get_nav_menu_object($locations[$location]);

    return $menu->name;
}

/**
 * Array map with preserved keys
 * @param  function $callback Callback function
 * @param  array    $array    Array to iterate over
 * @return array              Newly mapped array
 */
function array_map_with_keys($callback, $array)
{
    $keys = array_keys($array);

    return array_combine($keys, array_map($callback, $keys, $array));
}

/**
 * Dump and Die
 * @param array $args List of fields to debug
 */
function dd(...$args)
{
    foreach ($args as $arg) {
        dump($arg);
    }

    exit;
}

/**
 * Dump
 * @param mixed $arg Field to format and display
 */
function dump($arg)
{
    echo '<pre>';

    if (is_array($arg)) {
        print_r($arg);
    } else {
        var_dump($arg);
    }

    echo '</pre>';
}

/**
 * Set environment
 * @param string $env Override detection
 */
function setEnv($env = null)
{
    if (!is_null($env)) {
        define('ENV', $env);
        return;
    }

    if (isLocal()) {
        define('ENV', 'local');
    } elseif (isStaging()) {
        define('ENV', 'staging');
    } else {
        define('ENV', 'production');
    }
}

/**
 * Determine if environment is staging
 */
function isStaging()
{
    $siteUrl = get_site_url();

    if (strpos($siteUrl, 'smwp.ca') !== false) {
        return true;
    }

    return false;
}

/**
 * Determine if environment is local
 */
function isLocal()
{
    $siteUrl = get_site_url();

    if (strpos($siteUrl, '.vm') !== false) {
        return true;
    }

    if (strpos($siteUrl, '.local') !== false) {
        return true;
    }

    if (strpos($siteUrl, 'localhost') !== false) {
        return true;
    }

    return false;
}

/**
 * Convert alphanumeric phone numbers to numeric
 * @param string $phone
 */
function convertPhoneNumber($phone)
{
    $chars = str_split(strtolower($phone));
    $conversion = '';

    $alpha = [
        'a' => '2',
        'b' => '2',
        'c' => '2',
        'd' => '3',
        'e' => '3',
        'f' => '3',
        'g' => '4',
        'h' => '4',
        'i' => '4',
        'j' => '5',
        'k' => '5',
        'l' => '5',
        'm' => '6',
        'n' => '6',
        'o' => '6',
        'p' => '7',
        'q' => '7',
        'r' => '7',
        's' => '7',
        't' => '8',
        'u' => '8',
        'v' => '8',
        'w' => '9',
        'x' => '9',
        'y' => '9',
        'z' => '9',
    ];

    foreach ($chars as $char) {
        if (array_key_exists($char, $alpha)) {
            $conversion .= $alpha[$char];
        } else {
            $conversion .= $char;
        }
    }

    return preg_replace('/\D/', '', $conversion);
}
