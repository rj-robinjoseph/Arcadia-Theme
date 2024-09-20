<?php

/**
 * Below are the fields that may be passed into the partial
 * @param string 'class'   String of classes to be applied
 * @param string 'label'   Link label
 * @param string 'before'  Displayed before Label
 * @param string 'after'   Displayed after label
 */
if (Field::anyExist('link', 'link_file')) :
    $link = Field::equals('link_type', 'url') ? Field::get('link') : Field::get('link_file');

    echo '<a href="' . $link['url']. '"';
    Field::html('class', ' class="%s"');
    Field::html('style', ' style="%s"');

    if (!empty($link['title'])) :
        echo ' title="' . $link['title'] . '"';
    endif;

    if (!empty($link['alt'])) :
        echo ' alt="' . $link['alt'] . '"';
    endif;

    if (!empty($link['target'])) :
        echo ' target="' . $link['target'] . '"';
    endif;

    echo '>';
    Field::display('before');
    Field::display('label');
    Field::display('after');
    echo '</a>';
endif;
