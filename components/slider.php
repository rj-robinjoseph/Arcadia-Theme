<div class="<?php Layout::classes('slider'); ?>"<?php Layout::id(); ?>>
    <?php foreach (Field::iterable('slider') as $item) : ?>
        <div class="">
            <?php Field::image('slider_image', 'large'); ?>
        </div>
    <?php endforeach; ?>
</div>
