<?php

/**
 * @file
 * Display Suite 1 column template.
 */
?>
<div class="row <?php print $classes; ?>">
  <div class="container">
    <<?php print $ds_content_wrapper; print $layout_attributes; ?> class="ds-1col clearfix">
      <?php if (isset($title_suffix['contextual_links'])): ?>
        <?php print render($title_suffix['contextual_links']); ?>
      <?php endif; ?>
      <div class="row">
        <?php print $ds_content; ?>
      </div>
    </<?php print $ds_content_wrapper ?>>
    <?php if (!empty($drupal_render_children)): ?>
      <?php print $drupal_render_children ?>
    <?php endif; ?>
  </div>
</div>
