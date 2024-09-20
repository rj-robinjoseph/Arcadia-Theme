<?php get_header(); ?>

    <main class="main-content" role="main" id="main" tabindex="-1">

        <?php Banner::render([
            'type' => 'blog',
            'post_id' => get_option('page_for_posts'),
        ]); ?>

        <?php Layout::render([
            'default' => 'blog_index',
            'type' => 'blog',
            'post_id' => get_option('page_for_posts'),
        ]); ?>

    </main>

<?php get_footer(); ?>
