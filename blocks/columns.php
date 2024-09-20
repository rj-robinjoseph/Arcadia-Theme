<div class="<?php Layout::classes('columns'); ?>" style="<?php Layout::partial('background'); ?>"<?php Layout::id(); ?>>
    <?php Layout::partials('videobg', 'overlay'); ?>
    <div class="container">
        <div class="columns-container">
            <?php if (Field::exists('columns')) : ?>
                <?php foreach (Field::iterable('columns') as $index => $column) : ?>
                    <?php if ($index === 0) : ?>
                        <div class="column column-1">
                            <div class="inner">
                                <?php Layout::flexible(Field::get('content', []), 'components'); ?>
                            </div>
                        </div>
                    <?php elseif ($index === 1) : ?>
                        <div class="column column-2">
                            <div class="slider" id="slider-block">
                                <?php 
                                $slider_content = Field::get('content', []);
                                foreach ($slider_content as $content_item) {
                                    if ($content_item['acf_fc_layout'] === 'slider') {
                                        foreach ($content_item['slider'] as $slide) {
                                            $slider_image_id = $slide['slider_image'];
                                            if (!empty($slider_image_id)) {
                                                $slider_image_url = wp_get_attachment_image_url($slider_image_id, 'full');
                                                if ($slider_image_url) {
                                                    echo "<div class='slide'>";
                                                    echo "<img src='" . esc_url($slider_image_url) . "' alt='Slider Image'>";
                                                    echo "</div>";
                                                }
                                            }
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    jQuery(function ($) {
    $("#slider-block").slick({
        speed: 300,
        autoplay: true,
        autoplaySpeed: 5000,
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: false,
        fade: true,
        adaptiveHeight: true,
        appendDots: $(".controls .dots"),
    });
});
</script>