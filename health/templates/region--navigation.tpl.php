<?php
/**
 * @file
 * Returns the HTML for the footer region.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728140
 */
?>
<?php if ($content): ?>
  <nav id="nav" class="<?php print $classes; ?>" >
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <?php print $content; ?>
        </div>
      </div>
    </div>
  </nav>
<?php endif; ?>
