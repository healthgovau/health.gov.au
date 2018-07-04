<?php

/**
 * @file
 * Display Suite 2 column template.
 */
?>
<div class="container">
  <div class="row">
    <div <?php print $layout_attributes; ?> class=" au-callout <?php print $classes;?> clearfix">

      <?php if (isset($title_suffix['contextual_links'])): ?>
        <?php print render($title_suffix['contextual_links']); ?>
      <?php endif; ?>

      <div class="col-sm-3<?php print $left_classes; ?>">
        <?php print $left; ?>
      </div>

      <div class="col-sm-9<?php print $right_classes; ?>">
        <?php print $right; ?>
      </div>

    </div>

    <?php if (!empty($drupal_render_children)): ?>
      <?php print $drupal_render_children ?>
    <?php endif; ?>
  </div>
</div>
