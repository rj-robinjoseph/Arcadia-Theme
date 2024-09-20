<div class="<?php Layout::classes('banner banner-home'); ?>" style="<?php Layout::partial('background'); ?>">
    <?php Layout::partials('videobg', 'overlay'); ?>
    <div class="container">
        <?php Field::html('banner_title', '<h1>%s</h1>'); ?>
        <?php Field::display('banner_desc'); ?>
    </div>
</div>
