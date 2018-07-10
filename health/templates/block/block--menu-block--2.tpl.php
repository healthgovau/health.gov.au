<?php
/**
 * @file
 * Returns the HTML for a block.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728246
 */
?>

<div class="au-local-navigation__mobile-toggle au-accordion">
  <a href="#" class="au-accordion__title au-accordion--closed" aria-controls="block-menu-block-2">
    <span>In this section</span>
  </a>
</div>

<div class="<?php print $classes; ?> au-accordion au-accordion__body"<?php print $attributes; ?> id="<?php print $block_html_id; ?>">
  <?php print $content; ?>
</div>
