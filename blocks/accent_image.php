<div class="<?php Layout::classes('accent-image'); ?>" style="<?php Layout::partial('background'); ?>"<?php Layout::id(); ?>>
    <?php Layout::partials('videobg', 'overlay'); ?>
    <div class="row">
            <?php Field::image('accent', 'large'); ?>
        <div class="contents">
            <?php Layout::partial('title'); ?>
            <?php Field::display('content'); ?>
            <?php Layout::partial('buttons'); ?>
            <div class="sub_contents">
            <?php if (Field::exists('sub_contents')) : ?>
                <div class="text-column-items">
                    <?php foreach (Field::iterable('sub_contents') as $sub_content) : ?>
                        <div class="text-column-item">
                            <?php 
                                $icon = $sub_content['icon'];
                                if (!empty($icon)) {
                                    $icon_url = $icon['url'];
                                    echo '<img src="' . esc_url($icon_url) . '" class="icon" alt="">';
                                }
                            ?>
                            <?php Field::html('icon_heading', '<h1 class="icon_heading">%s</h1>'); ?>
                            <?php Field::display('icon_sub_heading'); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>
