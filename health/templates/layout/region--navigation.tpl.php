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
  <nav class="au-main-nav" aria-label="main navigation">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div id="main-nav-default" class="au-main-nav__content">
            <button
              aria-controls="main-nav-default"
              class="au-main-nav__toggle au-main-nav__toggle--open"
              onClick="return AU.mainNav.Toggle( this )">
              <i class="fa fa-bars"></i> Menu
            </button>
            <div class="au-main-nav__menu">
              <div class="au-main-nav__menu-inner">
                <div class="au-main-nav__focus-trap-top"></div>
                <button
                  aria-controls="main-nav-default"
                  class="au-main-nav__toggle au-main-nav__toggle--close"
                  onClick="return AU.mainNav.Toggle( this )">
                  <i class="fa fa-times"></i> Close
                </button>
                <?php print $content?>
                <div class="au-main-nav__focus-trap-bottom"></div>
              </div>
            </div>
            <button class="au-main-nav__toggle au-main-nav__toggle--search au-main-nav__toggle--open">
              <i class="fa fa-search"></i> Search
            </button>
            <div
              class="au-main-nav__overlay"
              aria-controls="main-nav-default"
              onClick="return AU.mainNav.Toggle( this )">
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
<?php endif; ?>
