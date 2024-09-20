<div class="content">
<?php Field::html('heading', '<h2 class="heading">%s</h2>'); ?>
    <div class="<?php Layout::classes('content-grid'); ?>" style="<?php Layout::partial('background'); ?>"<?php Layout::id(); ?>>
        <?php Layout::partials('videobg', 'overlay'); ?>
        <div class="container">
            <?php if (Field::exists('items')) : ?>
                <div class="items <?php Field::html('items_per_row', 'cols-%s', 'auto'); ?>">
                    <?php foreach (Field::iterable('items') as $item) : ?>
                        <div class="item">
                            <div class="round">
                                <?php Field::image('image', 'medium', ['class' => 'thumbnail']); ?>
                            </div>
                            <?php Field::html('label', '<h4 class="label">%s</h4>'); ?>
                            <?php Field::display('description'); ?>
                            <?php Layout::partial('button'); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php Layout::partial('button'); ?>
        </div>
    </div>
</div>