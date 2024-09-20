<?php
/*
 * Template Name: Home
 */

get_header(); ?>

    <main class="main-content" role="main" id="main" tabindex="-1">

        <?php if (have_posts()) :
            while (have_posts()) :
                the_post();

                Banner::render([
                    'group' => 'group_582d17dfe568d',
                    'file'  => 'banner_home',
                ]);

                Layout::render();
            endwhile;
        endif; ?>

    </main>

<?php get_footer(); ?>
