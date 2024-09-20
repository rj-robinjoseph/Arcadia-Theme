<?php $prefix = Field::exists('prefix') ? Field::get('prefix') . '_' : ''; ?>
<?php if (Field::anyExist($prefix . 'bg_video_webm', $prefix . 'bg_video_mp4')) : ?>
    <div class="video-bg">
        <video playsinline autoplay muted loop>
            <?php Field::html($prefix . 'bg_video_webm', '<source src="%s" type="video/webm">'); ?>
            <?php Field::html($prefix . 'bg_video_mp4', '<source src="%s" type="video/mp4">'); ?>
        </video>
    </div>
<?php endif; ?>
