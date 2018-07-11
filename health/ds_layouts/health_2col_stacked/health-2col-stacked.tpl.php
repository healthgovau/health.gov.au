<?php

/**
 * @file
 * Display Suite 2 column template.
 */
?>
<div class="row">
  <div class="container">
    <div class="row">
      <div <?php print $layout_attributes; ?> class="<?php print $classes;?> clearfix">

        <?php if (isset($title_suffix['contextual_links'])): ?>
          <?php print render($title_suffix['contextual_links']); ?>
        <?php endif; ?>

        <div class="group-header<?php print $top_classes; ?>">
          <?php print $top; ?>
        </div>

        <div class="group-left<?php print $left_classes; ?>">
          <?php print $left; ?>
        </div>

        <div class="group-right<?php print $right_classes; ?>">
          <?php print $right; ?>
        </div>

        <div class="group-footer<?php print $bottom_classes; ?>">
          <?php print $bottom; ?>
        </div>

      </div>

      <?php if (!empty($drupal_render_children)): ?>
        <?php print $drupal_render_children ?>
      <?php endif; ?>
    </div>
  </div>
</div>
