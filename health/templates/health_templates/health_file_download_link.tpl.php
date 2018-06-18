<span class="file-download">
  <?php print $icon; ?>
  <a
    href="<?php print url($uri['path'], $uri['options'])?>"
    <?php print drupal_attributes($uri['options']['attributes']);?>>
    <span class="file-download__visible-link">
      Download
      <span class="sr-only"><?php print $title?> as</span>
      <?php print $mime; ?></span>

     - <?php print $size?>, <?php print $pages?>
  </a>
</span>