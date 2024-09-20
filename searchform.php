<form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
    <label for="s" class="screen-reader-text"><?php _e('Search for:', DOMAIN); ?></label>
    <input type="search" id="s" name="s" value="" />

    <input type="submit" value="<?php _e('Search', DOMAIN); ?>" id="searchsubmit" />
</form>
