<?php get_header(); ?>

    <main class="main-content" role="main" id="main" tabindex="-1">

        <?php if (have_posts()) :
            while (have_posts()) :
                the_post();

                Banner::render();
                Layout::render();
            endwhile;
        endif; ?>

    </main>

<?php get_footer(); ?>
