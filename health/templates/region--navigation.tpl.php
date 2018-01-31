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
  <nav id="nav" class="<?php print $classes; ?>">
    <div class="container">
      <div class="row">

        <button class="mobile-toggle mobile-toggle__main-menu col-xs-6"
                aria-controls="block-superfish-1" aria-expanded="false"
                aria-selected="false" role="tab">
          Open menu
        </button>

        <button class="mobile-toggle mobile-toggle__search col-xs-6"
                aria-controls="search-api-page-search-form"
                aria-expanded="false" aria-selected="false" role="tab">
          Open search
        </button>
      </div>
      <div class="row">
        <?php print $content; ?>
      </div>
    </div>
  </nav>
<?php endif; ?>
