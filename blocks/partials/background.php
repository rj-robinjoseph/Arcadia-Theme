<?php

$prefix = Field::exists('prefix') ? Field::get('prefix') . '_' : '';
$styles = [];

if (Field::exists($prefix . 'bg_colour')) {
    $styles[] = 'background-color: ' . Field::get($prefix . 'bg_colour');
}

if (Field::exists($prefix . 'bg_image')) {
    $styles[] = 'background-image: url(' . Field::src($prefix . 'bg_image', 'banner') . ')';
}

if (Field::equals($prefix . 'class_bg_position', 'bg-pos-custom')) {
    $styles[] = 'background-position: ' . Field::get($prefix . 'bg_position_x') . '% ' . Field::get($prefix . 'bg_position_y') . '%';
}

if ($styles) {
    echo implode(";", $styles);
}
