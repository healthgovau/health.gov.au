<?php

/**
 * @file
 * Returns the HTML for a block.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728246
 */
?>
<aside class="au-side-nav au-accordion">
  <a
    href="#nav-default" class="au-side-nav__toggle au-accordion__title au-accordion--closed"
    aria-controls="nav-default"
    aria-expanded="false"
    aria-selected="false"
    role="tab"
    onclick="return AU.accordion.Toggle( this )"
  >
    In this section
  </a>
  <div id="nav-default" class="au-side-nav__content au-accordion--closed au-accordion__body">
    <?php print render($title_prefix); ?>
    <?php if ($title): ?>
      <h2 class="au-sidenav__title">
        <?php print $title; ?>
      </h2>
    <?php endif; ?>
    <?php print render($title_suffix); ?>
    <?php print $content; ?>
  </div>
</aside>
