<?php get_header(); ?>

    <main class="main-content" role="main" id="main" tabindex="-1">

        <?php Banner::render([
            'type' => 'results',
        ]); ?>

        <?php Layout::render([
            'default' => 'search_results',
            'type' => 'results',
        ]); ?>

    </main>

<?php get_footer(); ?>
