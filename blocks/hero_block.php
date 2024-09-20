<div class="<?php Layout::classes('hero-block'); ?>" style="<?php Layout::partial('background'); ?>"<?php Layout::id(); ?>>
    <?php Layout::partials('videobg', 'overlay'); ?>
    <div class="container">
        <div class="inner">
            <?php Layout::partial('title'); ?>
            <?php Field::display('description'); ?>
            <?php Layout::partial('buttons'); ?>
        </div>
    </div>
</div>
