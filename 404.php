<?php get_header(); ?>

    <main class="main-content" role="main" id="main" tabindex="-1">
        <?php Banner::render([
            'type' => 'not_found',
        ]); ?>

        <?php Layout::render([
            'default' => '404',
            'type' => 'not_found',
        ]); ?>
    </main>

<?php get_footer(); ?>
