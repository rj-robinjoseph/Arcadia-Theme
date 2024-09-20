<header class="header" role="banner">
    <div class="container">
        <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home" class="logo">
            <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>">
        </a>

        <div class="navs">
            <?php if (get_field('social_platforms', 'option')) : ?>
                <div class="social">
                    <?php Layout::partial('social'); ?>
                </div>
            <?php endif; ?>
            <div class="nav-items">
            <nav class="navigation" role="navigation" aria-label="site">
                <?php wp_nav_menu([
                    'theme_location' => 'primary',
                    'depth' => 2,
                ]); ?>
            </nav>
            <button>1-833-793-0890 | Member Access</button>
            </div>
            
        </div>
    </div>
</header>
