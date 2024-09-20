<?php $prefix = Field::exists('prefix') ? Field::get('prefix') . '_' : ''; ?>
<?php if (Field::exists($prefix . 'bg_overlay')) : ?>
    <div class="overlay" style="background-color: <?php Field::display($prefix . 'bg_overlay'); ?>;"></div>
<?php endif; ?>
