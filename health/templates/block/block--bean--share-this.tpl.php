<?php
/**
 * @file
 * Returns the HTML for a block.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728246
 */
?>
<div class="au-band au-band--short au-band--neutral-1 print--hide <?php print $classes; ?>"<?php print $attributes; ?> id="<?php print $block_html_id; ?>">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <?php print render($title_prefix); ?>
        <?php if ($title): ?>
          <h3<?php print $title_attributes; ?>><?php print $title; ?></h3>
        <?php endif; ?>
        <?php print render($title_suffix); ?>
        <?php print $content; ?>
    </div>
  </div>
</div>