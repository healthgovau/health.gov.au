<?php
/**
 * @file
 * Returns the HTML for a block.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728246
 */
?>
<div class="row">
  <div class="container <?php print $classes; ?>"<?php print $attributes; ?> id="<?php print $block_html_id; ?>">
    <div class="row">
      <div class="col-xs-12">
        <?php print render($title_prefix); ?>
        <?php if ($title): ?>
          <h3<?php print $title_attributes; ?>><?php print $title; ?></h3>
        <?php endif; ?>
        <?php print render($title_suffix); ?>

        <?php if ($content): ?>
        <div class="au-page-alerts au-page-alerts--info">
        <?php print $content; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>