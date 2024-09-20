<?php
/**
 * Post helper
 */
class Post
{
    /**
     * Recent posts
     * @param  integer $number Number of posts to fetch
     * @param  string  $type   Custom post type
     * @return array           Post collection
     */
    public static function recent($number = 1, $type = 'post', $options = [])
    {
        $posts = new WP_Query(array_merge([
            'post_type' => $type,
            'posts_per_page' => $number,
        ], $options));

        wp_reset_query();

        return $posts->get_posts();
    }

    /**
     * Iterate over posts
     */
    public static function iterable(...$args)
    {
        global $post;

        $posts = self::recent(...$args);

        Loop::init(count($posts));

        foreach ($posts as $key => $post) {
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
}
