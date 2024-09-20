<?php

namespace Arcadia\Theme;

/**
 * Extend search
 */
class Search
{
    /**
     * Keep track of search query
     * @var boolean
     */
    private static $search_where = false;

    /**
     * Setup hooks for front end
     * @return void
     */
    public static function init()
    {
        if (!is_admin()) {
            add_filter('posts_join', [__CLASS__, 'join']);
            add_filter('posts_where', [__CLASS__, 'where']);
            add_filter('posts_distinct', [__CLASS__, 'distinct']);
        }
    }

    /**
     * Join posts and postmeta tables
     * @link   http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_join
     * @param  string $join Current query
     * @return string       Extended query
     */
    public static function join($join)
    {
        global $wpdb;

        if (is_search() && self::$search_where) {
            $join .=' LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
        }

        return $join;
    }

    /**
     * Modify the search query with posts_where
     * @link   http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_where
     * @param  string $join Current query
     * @return string       Extended query
     */
    public static function where($where)
    {
        global $pagenow, $wpdb;

        if (is_search() && strpos($where, 'post_content') !== false) {
            self::$search_where = true;

            $where = preg_replace(
                "/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
                "(" . $wpdb->posts.".post_title LIKE $1) OR (" . $wpdb->postmeta . ".meta_value LIKE $1)",
                $where
            );
        }

        return $where;
    }

    /**
     * Prevent duplicates
     * @link   http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_distinct
     * @param  string $join Current query
     * @return string       Extended query
     */
    public static function distinct($where)
    {
        global $wpdb;

        if (is_search() && self::$search_where) {
            self::$search_where = false;
            return "DISTINCT";
        }

        return $where;
    }
}
