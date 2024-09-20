<?php get_header(); ?>

    <main class="main-content" role="main" id="main" tabindex="-1">

        <?php Banner::render([
            'type' => 'archives',
        ]); ?>

        <?php Layout::render([
            'default' => 'blog_index',
            'type' => 'archives',
        ]); ?>

    </main>

<?php get_footer(); ?>
