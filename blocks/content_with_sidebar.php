<div class="<?php Layout::classes('content-with-sidebar'); ?>" style="<?php Layout::partial('background') ?>"<?php Layout::id(); ?>>
    <?php Layout::partials('videobg', 'overlay'); ?>
    <div class="container">
        <div class="content">
            <?php Layout::flexible(Field::get('content.content', []), 'components'); ?>
        </div>
        <div class="sidebar">
            <?php
                $sidebar_content = Field::get('sidebar.content', []);
                foreach ($sidebar_content as $component) {
                    if ($component['acf_fc_layout'] == 'link') {
                        $link = $component['link'];
                        if (!empty($link['title']) && !empty($link['url'])) {
                            echo '<a href="' . esc_url($link['url']) . '" target="' . esc_attr($link['target']) . '">'
                                 . esc_html($link['title']) . '</a>';
                        }
                    }
                }
            ?>
        </div>
    </div>
</div>
