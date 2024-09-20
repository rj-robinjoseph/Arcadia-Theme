<?php

// Look Up Widget
$widget = new WP_Query([
    'post_type' => 'widget',
    'p' => Field::get('widget'),
]);

// Display appropriate blocks
if ($widget->have_posts()) {
    while ($widget->have_posts()) {
        $widget->the_post();

        $blocks = get_field('components');

        Layout::flexible($blocks['content'], 'components');
    }
}

wp_reset_query();
