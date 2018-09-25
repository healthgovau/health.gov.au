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
  <div class="<?php print $classes; ?>">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <?php print $content; ?>
          <a href="#" id="close-health-alert" class="close-toggle" title="Close health alert">Close</a>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
