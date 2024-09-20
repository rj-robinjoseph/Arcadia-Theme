<div class="content-with-sidebar side-right pt-normal pb-normal">
    <div class="container">
        <div class="content">
            <div class="block-title">
                <h2 class="title-normal"><?php the_title(); ?></h2>
            </div>
            <?php the_content(); ?>
        </div>
        <aside class="sidebar">
            <div class="block-title">
                <h3 class="title-small"><?php _e('Related News', DOMAIN); ?></h3>
            </div>
            <?php

            $articles = new WP_Query([
                'posts_per_page' => 8,
                'post__not_in' => [
                    get_the_ID(),
                ],
            ]);

            if ($articles->have_posts()) :
                echo '<ul>';

                while ($articles->have_posts()) :
                    $articles->the_post();
                    echo '<li><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></li>';
                endwhile;

                echo '</ul>';
            endif;

            ?>
        </aside>
    </div>
</div>
