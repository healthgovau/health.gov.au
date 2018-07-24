<?php

/**
 * @file
 * Display Suite 1 column template.
 */
?>
<div class="row para-row au-body <?php print $classes; ?>">
  <<?php print $ds_content_wrapper; print $layout_attributes; ?> class="container ds-1col clearfix">
    <?php if (isset($title_suffix['contextual_links'])): ?>
      <?php print render($title_suffix['contextual_links']); ?>
    <?php endif; ?>
    <div class="au-card-list au-card-list--matchheight">
      <?php print $ds_content; ?>
    </div>
  </<?php print $ds_content_wrapper ?>>
  <?php if (!empty($drupal_render_children)): ?>
      <?php print $drupal_render_children ?>
  <?php endif; ?>
</div>
