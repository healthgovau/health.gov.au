<?php

/**
 * @file
 * Display Suite 2 column template.
 */
?>
<div class="para-container <?php print $classes; ?>">
  <<?php print $layout_wrapper; print $layout_attributes; ?> class="inside-para-container ds-2col <?php print $classes;?> clearfix">

  <div class="para-row">

  <?php if (isset($title_suffix['contextual_links'])): ?>
    <?php print render($title_suffix['contextual_links']); ?>
  <?php endif; ?>

  <<?php print $left_wrapper ?> class="group-left<?php print $left_classes; ?>">
  <?php print $left; ?>
</<?php print $left_wrapper ?>>

<<?php print $right_wrapper ?> class="group-right<?php print $right_classes; ?>">
<?php print $right; ?>
</<?php print $right_wrapper ?>>

</div>
</<?php print $layout_wrapper ?>>

<?php if (!empty($drupal_render_children)): ?>
  <?php print $drupal_render_children ?>
<?php endif; ?>
</div>
