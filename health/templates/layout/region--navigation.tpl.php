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
  <nav id="nav" role="navigation" class="au-main-nav__wrapper <?php print $classes; ?>">
    <div class="container">
      <div class="row">

        <button
          class="au-main-nav__mobile-toggle au-main-nav__mobile-toggle--main-menu col-xs-6"
          aria-controls="block-superfish-1" role="tab">
          <span class="mobile-toggle__label">Menu</span>
        </button>

        <button
          class="au-main-nav__mobile-toggle au-main-nav__mobile-toggle--search col-xs-6"
          aria-controls="block-search-api-page-default-search--2"
          role="tab">
          <span class="mobile-toggle__label">Search</span>
        </button>
      </div>
      <div class="row">
        <?php print $content; ?>
      </div>
    </div>
  </nav>
<?php endif; ?>
