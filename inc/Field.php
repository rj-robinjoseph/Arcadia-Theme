<?php

/**
 * Content Rending
 */
class Field
{
    /**
     * Block Data
     * @var array
     */
    private static $data = [];

    /**
     * History
     * @var array
     */
    private static $history = [];

    /**
     * Store Data
     * @param  array $data Data to store
     * @return void
     */
    public static function setData($data = [])
    {
        self::$history[] = self::getAll();
        self::$data = $data;
    }

    /**
     * Extend existing data set
     * @param  array $data Additional data
     * @return void
     */
    public static function extend($data = [])
    {
        self::$data = array_merge(self::$data, $data);
    }

    /**
     * Restore previous data set
     * @return void
     */
    public static function restore()
    {
        self::$data = array_pop(self::$history);
    }

    /**
     * Return entire data set
     * @return array Data set
     */
    public static function getAll()
    {
        return self::$data;
    }

    /**
     * Spit out formatted data
     * @return void
     */
    public static function debug()
    {
        dump(self::getAll());
    }

    /**
     * Spit out formatted data
     * @param  string $field   Field name
     * @return void
     */
    public static function dump($field)
    {
        dump(self::get($field));
    }

    /**
     * Retrieve Data
     * @param  string $field   Field name
     * @param  string $default Default value when non existant
     * @return mixed
     */
    public static function get($field, $default = '')
    {
        $fields = explode('.', $field);
        $data = self::$data;

        while ($fields) {
            $key = array_shift($fields);
            $data = array_key_exists($key, $data) ? $data[$key] : null;

            if (empty($data)) {
                return $default;
            }
        }

        return $data;
    }

    /**
     * Fetch a key from the previous dataset
     * @param  string $field   Field name
     * @param  string $default Default value when non existant
     * @return mixed
     */
    public static function parent($field, $default = '')
    {
        $dataset = end(self::$history);

        if (array_key_exists($field, $dataset)) {
            return $dataset[$field];
        }

        return $default;
    }

    /**
     * Display field to screen
     * @param  string $field   Field name
     * @param  string $default Default value when non existant
     * @return void
     */
    public static function display($field, $default = '')
    {
        echo self::get($field, $default);
    }

    /**
     * Display field wrapped in HTML if exists
     * @param  string $field   Field name
     * @param  string $wrap    Formatted string
     * @param  string $default Default value if field not set
     * @return void
     */
    public static function html($field, $wrap = '%s', $default = '')
    {
        if (!self::exists($field)) {
            echo !empty($default) ? sprintf($wrap, $default) : '';
            return;
        }

        echo sprintf($wrap, self::get($field));
    }

    /**
     * Display string wrapped in HTML
     * @param  string $field Field name
     * @param  string $val   Value to check against
     * @param  string $pass  Success string
     * @param  string $fail  Failure string
     * @return void
     */
    public static function displayIfEquals($field, $val, $pass = '%s', $fail = '')
    {
        echo self::equals($field, $val) ? sprintf($pass, self::get($field)) : sprintf($fail, self::get($field));
    }

    /**
     * Check existance of key
     * @param  string  $field Field name
     * @return boolean
     */
    public static function hasKey($field)
    {
        return array_key_exists($field, self::$data);
    }

    /**
     * Check existance of key and it's contents
     * @param  string  $field Field name
     * @return boolean
     */
    public static function exists($field)
    {
        if (strpos($field, '.') !== false) {
            $field = explode('.', $field)[0];
        }

        return self::hasKey($field) && self::$data[$field];
    }

    /**
     * Check existance of all fields
     * @param  array   $fields Names of fields
     * @return boolean
     */
    public static function allExist(...$fields)
    {
        return count(self::keepExists(...$fields)) === count($fields);
    }

    /**
     * Check existance of any field
     * @param  array   $fields Name of fields
     * @return boolean
     */
    public static function anyExist(...$fields)
    {
        return !empty(self::keepExists(...$fields));
    }

    /**
     * Check field equals
     * @param  string  $field Field name
     * @param  string  $val   Value to check against
     * @return boolean
     */
    public static function equals($field, $val)
    {
        return self::get($field) == $val;
    }

    /**
     * Fetch source for an image
     * @param  string  $field Field name
     * @param  mixed   $size  Image Size
     * @param  boolean $crop
     * @return string         Image source
     */
    public static function src($field, $size = 'full', $crop = false)
    {
        if (is_array($size)) {
            $src = self::createImageSize(self::get($field), $size, $crop);
        } else {
            $src = wp_get_attachment_image_src(self::get($field), $size);
        }

        return $src ? $src[0] : false;
    }

    /**
     * Make sure HTTP has been added to URL
     * @param  string $field URL field
     * @return string        Fixed URL
     */
    public static function url($field)
    {
        if (!preg_match('/^(\#|http|\/\/|mailto:|tel:)/i', self::get($field))) {
            return 'http://' . self::get($field);
        }

        return self::get($field);
    }

    /**
     * Display Image
     * @todo   srcset
     * @param  string  $field Field name
     * @param  mixed   $size  Image Size
     * @param  array   $attrs Additional attributes
     * @param  boolean $crop
     * @return void
     */
    public static function imageTag($field, $size = 'full', $attrs = [], $crop = false)
    {
        if (!self::exists($field)) {
            return;
        }

        $alt = get_post_meta(self::get($field), '_wp_attachment_image_alt', true);

        $img = '<img src="' . self::src($field, $size, $crop) . '" alt="' . $alt . '"';

        array_walk($attrs, function ($item, $key) use (&$img) {
            $img .= ' ' . $key . '="' . $item . '"';
        });

        $img .= '>' . PHP_EOL;

        return $img;
    }

    /**
     * Display Image
     * @todo   srcset
     * @param  string  $field Field name
     * @param  mixed   $size  Image Size
     * @param  array   $attrs Additional attributes
     * @param  boolean $crop
     * @return void
     */
    public static function image($field, $size = 'full', $attrs = [], $crop = false)
    {
        if (!self::exists($field)) {
            return;
        }

        echo self::imageTag($field, $size, $attrs, $crop);
    }

    /**
     * Display cropped image
     * @todo   srcset
     * @param  string  $field Field name
     * @param  mixed   $size  Image Size
     * @param  array   $attrs Additional attributes
     * @return void
     */
    public static function croppedImage($field, $size = 'full', $attrs = [])
    {
        self::image($field, $size, $attrs, true);
    }

    /**
     * Render shortcode
     * @param  string $field Field name
     * @param  string $wrap  Formatted string
     * @return void
     */
    public static function shortcode($field, $wrap = '%s')
    {
        if (!self::exists($field)) {
            return;
        }

        echo do_shortcode(sprintf($wrap, self::get($field)));
    }

    /**
     * Check whether field is an array
     * @param  string  $field Field name
     * @return boolean
     */
    public static function isArray($field)
    {
        return self::exists($field) && is_array(self::get($field));
    }

    /**
     * Count children of field
     * @param  string $field Field name
     * @return mixed         Current count or null on non array
     */
    public static function count($field)
    {
        return self::isArray($field) ? count(self::get($field)) : null;
    }

    /**
     * Iterate over field
     * @param  string $field Field name
     * @return void
     */
    public static function iterable($field)
    {
        if (!self::exists($field)) {
            return [];
        }

        Loop::init(self::count($field));

        foreach (self::get($field) as $key => $data) {
            if ($key > 0) {
                Field::restore();
                Loop::iterate();
            }

            Field::setData($data);
            yield $key => $data;
        }

        Field::restore();
        Loop::restore();
    }

    /**
     * Iterate over field setting up post data
     * @param  string $field Field name
     * @return void
     */
    public static function relationship($field)
    {
        if (!self::exists($field)) {
            return [];
        }

        global $post;

        Loop::init(self::count($field));

        foreach (self::get($field) as $key => $post) {
            if ($key > 0) {
                Field::restore();
                Loop::iterate();
            }

            setup_postdata($post);

            Field::setData(get_fields());
            yield $key => $post;
        }

        wp_reset_postdata();
        Field::restore();
        Loop::restore();
    }

    /**
     * Convert field into a kebab case string
     * @param  string $field
     * @param  string $separator
     * @return string
     */
    public static function slug($field, $separator = '-')
    {
        $title = self::get($field);

        if (!is_string($title)) {
            return $title;
        }

        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', strtolower($title));
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        return trim($title, $separator);
    }

    /**
     * Trim out non existant fields
     * @param  array $fields Fields to check
     * @return array         Populated fields
     */
    private static function keepExists(...$fields)
    {
        return array_filter($fields, function ($field) {
            return self::exists($field);
        });
    }

    /**
     * Generate exact image size
     * @param  integer  $image_id Attachment ID
     * @param  array    $size     Image size [width, height]
     * @param  boolean  $crop     Force exact dimensions
     * @return array              Image meta
     */
    private static function createImageSize($image_id, $size, $crop = false)
    {
        list($width, $height) = $size;

        // Temporarily create an image size
        $size_id = 'lazy_' . $width . 'x' .$height . '_' . ((string) $crop);

        add_image_size($size_id, $width, $height, $crop);

        // Get the attachment data
        $meta = wp_get_attachment_metadata($image_id);

        // If the size does not exist
        if (!isset($meta['sizes'][$size_id])) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            $file     = get_attached_file($image_id);
            $new_meta = wp_generate_attachment_metadata($image_id, $file);

            // Merge the sizes so we don't lose already generated sizes
            $new_meta['sizes'] = array_merge($meta['sizes'], $new_meta['sizes']);

            // Update the meta data
            wp_update_attachment_metadata($image_id, $new_meta);
        }

        // Fetch the sized image
        $sized = wp_get_attachment_image_src($image_id, $size_id);

        // Remove the image size so new images won't be created in this size automatically
        remove_image_size($size_id);

        return $sized;
    }
}
