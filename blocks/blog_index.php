<div class="content-with-sidebar side-right pt-normal pb-normal">
    <div class="container">

        <div class="content posts-listing">

            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                    <article <?php post_class() ?> id="post-<?php the_ID(); ?>">
                        <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
                        <div class="posted">
                            <?php printf(
                                __('<time class="entry-date" datetime="%1$s" pubdate>%2$s</time>', ''),
                                esc_attr(get_the_date('c')),
                                esc_html(get_the_date())
                            ); ?>

                            <div class="categories"><?php the_category(', '); ?></div>
                        </div>
                        <?php the_excerpt(); ?>
                        <p class="read-more"><a href="<?php the_permalink(); ?>"><strong><?php _e('Read Article', DOMAIN); ?></strong></a></p>
                    </article>

                <?php endwhile; ?>
            <?php else : ?>

                <h2><?php _e('Blog empty', DOMAIN); ?></h2>
                <p><?php _e('There currently aren\'t any posts published.', DOMAIN); ?></p>

            <?php endif; ?>

            <div class="pagination">
                <?php echo get_previous_posts_link('Newer Articles'); ?>
                <?php echo get_next_posts_link('Older Articles'); ?>
            </div>

        </div>
        <aside class="sidebar posts-sidebar">
            <div class="block-title">
                <h3 class="title-normal"><?php _e('Categories', DOMAIN); ?></h3>
            </div>

            <ul class="categories">
                <?php wp_list_categories([
                    'orderby' => 'name',
                    'title_li' => '',
                ]); ?>
            </ul>

            <div class="block-title">
                <h3 class="title-normal"><?php _e('Archives', DOMAIN); ?></h3>
            </div>

            <ul class="archives">
                <?php wp_get_archives('type=monthly'); ?>
            </ul>
        </aside>

    </div>
</div>
