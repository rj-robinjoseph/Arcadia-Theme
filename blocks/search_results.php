<div class="search-results">
    <div class="container">
        <?php if (have_posts()) : ?>

            <?php while (have_posts()) :
                the_post(); ?>

                <article <?php post_class() ?> id="post-<?php the_ID(); ?>">
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                </article>

            <?php endwhile; ?>

        <?php else : ?>

            <h2><?php _e('Nothing Found', DOMAIN); ?></h2>

        <?php endif; ?>
    </div>
</div>
