<?php foreach (get_field('social_platforms', 'option') as $option) : ?>
    <a href="<?php echo $option['url']; ?>" target="_blank" rel="noopener" aria-label="Visit us on <?php echo $option['label']; ?>"><em class="fab fa-<?php echo $option['icon']; ?>" aria-hidden="true"></em></a>
<?php endforeach; ?>
