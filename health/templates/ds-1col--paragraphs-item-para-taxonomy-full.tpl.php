<?php

/**
 * @file
 * Display Suite 1 column template.
 */
?>
<div class="para-container <?php print $classes; ?>">
  <<?php print $ds_content_wrapper; print $layout_attributes; ?> class="inside-para-container ds-1col <?php print $classes;?> clearfix">
    <?php if (isset($title_suffix['contextual_links'])): ?>
      <?php print render($title_suffix['contextual_links']); ?>
    <?php endif; ?>
    <div class="para-row">
        <a href="<?php print $link_url; ?>" title="Link to term" rel="Link to term">
          <?php print $ds_content; ?>
        </a>
    </div>
  </<?php print $ds_content_wrapper ?>>
  <?php if (!empty($drupal_render_children)): ?>
    <?php print $drupal_render_children ?>
  <?php endif; ?>
</div>
