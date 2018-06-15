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
  <nav id="nav" role="navigation" class="<?php print $classes; ?>">
    <div class="container">
      <div class="row health-accordion health-accordion--skip-auto">

        <button
          class="mobile-toggle mobile-toggle--main-menu col-xs-6 js-au-accordion"
          aria-controls="block-superfish-1" role="tab">
          <span class="mobile-toggle__label">Open menu</span>
        </button>

        <button
          class="mobile-toggle mobile-toggle--search col-xs-6 js-au-accordion"
          aria-controls="block-search-api-page-default-search--2"
          role="tab">
          <span class="mobile-toggle__label">Open search</span>
        </button>
      </div>
      <div class="row">
        <?php print $content; ?>
      </div>
    </div>
  </nav>
<?php endif; ?>
