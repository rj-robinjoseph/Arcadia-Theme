<div class="<?php Layout::classes('content-with-sidebar'); ?>" style="<?php Layout::partial('background') ?>"<?php Layout::id(); ?>>
    <?php Layout::partials('videobg', 'overlay'); ?>
    <div class="container">
        <div class="content">
            <?php Layout::flexible(Field::get('content.content', []), 'components'); ?>
        </div>
        <div class="sidebar">
            <?php Layout::flexible(Field::get('sidebar.content', []), 'components'); ?>
        </div>
    </div>
</div>
