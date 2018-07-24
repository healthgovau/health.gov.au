<?php

/**
 * @file
 * Display Suite 2 column template.
 */
?>
<div class="row para-row">
<<?php print $layout_wrapper; print $layout_attributes; ?> class="ds-2col <?php print $classes;?> clearfix">

  <?php if (isset($title_suffix['contextual_links'])): ?>
  <?php print render($title_suffix['contextual_links']); ?>
  <?php endif; ?>

  <<?php print $left_wrapper ?> class="group-left col-xs-12 col-sm-4 col-lg-3 hotline<?php print $left_classes; ?>">
    <?php print $left; ?>
  </<?php print $left_wrapper ?>>

  <<?php print $right_wrapper ?> class="group-right col-xs-12 col-sm-8 col-lg-9 hotline<?php print $right_classes; ?>">
    <div class="au-callout">
      <?php print $right; ?>
    </div>
    <?php if (isset($node->link['title'])): ?>
        <div class="more-link au-cta-link col-xs-12">
          <?php print l($node->link['title'], $node->link['url']); ?>
        </div>
    <?php endif; ?>
  </<?php print $right_wrapper ?>>

</<?php print $layout_wrapper ?>>

<?php if (!empty($drupal_render_children)): ?>
  <?php print $drupal_render_children ?>
<?php endif; ?>
</div>
