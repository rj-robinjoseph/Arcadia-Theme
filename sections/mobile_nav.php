<nav class="mobile-navigation">
    <div class="container">
        <div class="main-menu">
            <?php wp_nav_menu([
                'theme_location' => 'mobile',
                'depth' => 1,
            ]); ?>

            <?php if (get_field('social_platforms', 'option')) : ?>
                <div class="social">
                    <?php Layout::partial('social'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="mobile-menu">
    <div class="mobile-menu-icon"></div>
</div>
