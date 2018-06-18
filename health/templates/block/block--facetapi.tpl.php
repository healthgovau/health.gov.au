<?php
/**
 * @file
 * Returns the HTML for a block.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728246
 */
?>
<div class="<?php print $classes; ?> health-accordion" <?php print $attributes; ?> id="<?php print $block_html_id; ?>">
    <?php print render($title_prefix); ?>
    <?php if ($title): ?>
    <a href="<?php print '#accordion-' . $block_html_id?>"
       class="health-accordion__title health-accordion--open"
       aria-controls="<?php print 'accordion-' . $block_html_id; ?>"
       aria-expanded="true"
       aria-selected="false"
       role="tab">
       <?php print $title; ?></a>
    <?php endif; ?>
    <?php print render($title_suffix); ?>

    <div class="health-accordion__body health-accordion--open" id="<?php print 'accordion-' . $block_html_id; ?>" aria-hidden="true">
      <div class="health-accordion__body-wrapper">
        <?php print $content; ?>
      </div>
    </div>
</div>
