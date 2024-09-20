<div class="<?php Layout::classes('map'); ?>"<?php Layout::id(); ?>>
    <div class="acf-map"<?php
        Field::html('icon', ' data-icon="%s"');
        Field::html('enable_clustering', 'data-cluster-path="' . get_template_directory_uri() . '/img/m"');
    ?>>
        <?php foreach (Field::iterable('markers') as $loop) :
            $location = Field::get('marker');

            ?>
            <div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>">
                <p class="address"><?php echo $location['address']; ?></p>
            </div>
        <?php endforeach; ?>
        <?php if (Field::exists('include_theme_settings') && get_field('locations', 'option')) : ?>
            <?php foreach (get_field('locations', 'option') as $address) :
                $location = $address['map_location'];

                ?>
                <div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>">
                    <p class="address"><?php echo $location['address']; ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
