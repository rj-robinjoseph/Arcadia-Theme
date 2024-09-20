<?php
$locations = get_field('locations', 'option');
$emergency_contact = get_field('emergency_contact', 'option');
$copyright = get_field('copyright', 'option');
?>
<footer class="footer" role="contentinfo">
    <div class="footer-container">
        <div class="emergency-contacts">
            <h6>Emergency Contact Numbers</h6>
            <ul>
                <?php if( $emergency_contact ): ?>
                    <?php foreach( $emergency_contact as $contact ): ?>
                        <li><?php echo $contact['label']; ?>: <?php echo $contact['number']; ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No emergency contacts found.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="address">
            <h6>Obishikokaang Address</h6>
            <?php if( $locations ): ?>
                <?php foreach( $locations as $location ): ?>
                    <p>
                        <?php echo $location['address']; ?>,<br>
                        <?php echo $location['city']; ?>, <?php echo $location['province']; ?><br>
                        <?php echo $location['postal_code']; ?><br>
                        Office: <?php echo $location['phone']; ?><br>
                        Fax: <?php echo $location['fax']; ?>
                    </p>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No locations found.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php if( $copyright ) : ?>
    <div class="copyright">
        <p>&copy; <?php echo date('Y'); ?> <?php echo $copyright; ?></p>
    </div>
<?php endif; ?>
</footer>
