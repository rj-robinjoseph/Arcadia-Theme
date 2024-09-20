<?php

if (Field::exists('btn_label')) :
    $fields = [
        'class' => 'btn',
        'label' => '<span class="btn-label">' . Field::get('btn_label') . '</span>',
    ];

    if (Field::exists('btn_icon')) :
        $fields['before'] = '<em class="far fa-' . Field::get('btn_icon') . '"></em>';
        $fields['class'] .= ' icon-' . Field::get('btn_icon_placement');
    elseif (!Field::equals('btn_icon_placement', 'none')) :
        $fields['before'] = '<em class="far fa-angle-right"></em>';
        $fields['class'] .= ' icon-after';
    endif;

    if (Field::exists('btn_color')) :
        $fields['class'] .= ' ' . Field::get('btn_color');
    endif;

    Layout::partial('link', $fields);
endif;
