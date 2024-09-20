<?php if (Field::exists('buttons')) : ?>
    <p class="btns">
        <?php

        foreach (Field::iterable('buttons') as $btn) :
            Layout::partial('button');
        endforeach;

        ?>
    </p>
<?php endif; ?>
