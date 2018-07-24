<?php

/**
 * @file
 * Display Suite 2 column template.
 */
?>
<div class="row" <?php print $classes; ?>">
  <<?php print $layout_wrapper; print $layout_attributes; ?> class="container ds-2col clearfix">

  <?php if (isset($title_suffix['contextual_links'])): ?>
    <?php print render($title_suffix['contextual_links']); ?>
  <?php endif; ?>

  <<?php print $left_wrapper ?> class="group-left col-xs-12 col-md-5<?php print $left_classes; ?>">
  <?php print $left; ?>
</<?php print $left_wrapper ?>>

<<?php print $right_wrapper ?> class="group-right col-xs-12 col-md-7<?php print $right_classes; ?>">
<?php print $right; ?>
</<?php print $right_wrapper ?>>

</<?php print $layout_wrapper ?>>

<?php if (!empty($drupal_render_children)): ?>
  <?php print $drupal_render_children ?>
<?php endif; ?>
</div>
