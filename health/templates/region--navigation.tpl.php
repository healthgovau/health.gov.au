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
        <a class="mobile-toggle mobile-toggle__main-menu col-xs-6">
          <span class="mobile-toggle__open"><i class="fas fa-bars"></i>Open menu</span>
          <span class="mobile-toggle__close"><i class="fas fa-times"></i>Close menu</span>
        </a>
        <a class="mobile-toggle mobile-toggle__search col-xs-6">
          <span class="mobile-toggle__open"><i class="fas fa-search"></i>Open search</span>
          <span class="mobile-toggle__close"><i class="fas fa-times"></i>Close search</span>
        </a>
      </div>
      <div class="row">
        <?php print $content; ?>
      </div>
    </div>
  </nav>
<?php endif; ?>
