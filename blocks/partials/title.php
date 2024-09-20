<?php if (Field::exists('title')) : ?>
    <div class="block-title <?php Field::display('title_alignment'); ?>">
        <?php Field::html('title', '<h2 class="' . Field::get('title_size', 'title-normal') . '">%s</h2>'); ?>
    </div>
<?php endif; ?>
