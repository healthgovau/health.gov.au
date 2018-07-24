<?php
/**
 * @file
 * Returns HTML for a region.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728112
 */
?>
<?php if ($content): ?>
  <div class="container <?php print $classes; ?>">
      <div class="row">
          <div class="au-card-list au-card-list--matchheight">
            <?php print $content; ?>
          </div>
      </div>
  </div>
<?php endif; ?>
