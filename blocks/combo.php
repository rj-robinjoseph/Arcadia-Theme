<div class="<?php Layout::classes('combo'); ?>" style="<?php Layout::partial('background'); ?>"<?php Layout::id(); ?>>
    <?php Layout::partials('videobg', 'overlay'); ?>
    <?php Layout::flexible(Field::get('blocks', [])); ?>
</div>
