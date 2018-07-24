<?php

/**
 * @file
 * Display Suite 1 column template.
 */
?>
<<?php print $ds_content_wrapper;
print $layout_attributes; ?> class="ds-1col stat <?php print $classes; ?> clearfix">

<?php if (isset($title_suffix['contextual_links'])): ?>
  <?php print render($title_suffix['contextual_links']); ?>
<?php endif; ?>

  <div class="stat__card">
    <?php print render($content['field_statistic_value']); ?>
    <?php print render($content['field_statistic_value_text']); ?>
    <div class="stat__trend">
      <?php print render($content['field_statistic_trend_text']); ?>
      <?php print render($content['field_statistic_trend_show_icon']); ?>
    </div>
  </div>

</<?php print $ds_content_wrapper ?>>

<?php if (!empty($drupal_render_children)): ?>
  <?php print $drupal_render_children ?>
<?php endif; ?>
