<div class="<?php Layout::classes('banner banner-default'); ?>" style="<?php Layout::partial('background'); ?>">
        <?php Layout::partials('videobg', 'overlay'); ?>
        <div class="container">
            <div class="text">
            <?php Field::html('banner_title', '<h1>%s</h1>'); ?>
            <?php Field::display('banner_desc'); ?>
            </div>
            <div class="image">
            <!-- <img src="<?php echo get_template_directory_uri(); ?>/img/main-banner.png" alt=""> -->
            </div>
        </div>
</div>