<span class="au-file">
  <?php print $icon; ?>
  <a class="au-file__link" data-title="<?php print $title?>"
    href="<?php print url($uri['path'], $uri['options'])?>"
    <?php print drupal_attributes($uri['options']['attributes']);?>>
    <span class="au-file__link--visible">Download <span class="sr-only"><?php print $title?> as </span><?php print $mime ?></span>
     - <?php print $size?>, <?php print $pages?>
  </a>
</span>