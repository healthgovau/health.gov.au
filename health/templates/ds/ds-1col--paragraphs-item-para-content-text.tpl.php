<?php

/**
 * @file
 * Display Suite 1 column template.
 */
?>
<div class="row">
  <div class="<?php print $classes; ?>">
    <div class="container">
      <div class="row">
        <<?php print $ds_content_wrapper; print $layout_attributes; ?> class="col-xs-12 ds-1col clearfix">
          <?php if (isset($title_suffix['contextual_links'])): ?>
            <?php print render($title_suffix['contextual_links']); ?>
          <?php endif; ?>
          <?php print $ds_content; ?>
        </<?php print $ds_content_wrapper ?>>
        <?php if (!empty($drupal_render_children)): ?>
          <?php print $drupal_render_children ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
