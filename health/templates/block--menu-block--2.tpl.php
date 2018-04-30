<?php
/**
 * @file
 * Returns the HTML for a block.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728246
 */
?>

<div class="health-accordion">
  <a href="#" class="localnav__mobile-toggle health-accordion__title health_accordion--closed" aria-controls="block-menu-block-2">
    <span>In this section</span>
  </a>
</div>

<div class="<?php print $classes; ?> health-accordion health-accordion__body health-accordion--closed"<?php print $attributes; ?> id="<?php print $block_html_id; ?>">

  <?php if ($block->region != 'sidebar_second') : ?>
    <?php print render($title_prefix); ?>
    <?php if ($title): ?>
      <h3<?php print $title_attributes; ?>><?php print $title; ?></h3>
    <?php endif; ?>
    <?php print render($title_suffix); ?>
  <?php endif; ?>

  <?php print $content; ?>
</div>
