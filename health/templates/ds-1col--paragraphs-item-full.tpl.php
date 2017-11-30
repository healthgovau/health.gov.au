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

  <?php print $ds_content; ?>
</<?php print $ds_content_wrapper ?>>

<?php if (!empty($drupal_render_children)): ?>
  <?php print $drupal_render_children ?>
<?php endif; ?>

</div>
