<?php

/**
 * @file
 * Display Suite 1 column template.
 */
?>
<<?php print $ds_content_wrapper;
print $layout_attributes; ?> class="ds-1col <?php print $classes; ?> clearfix">

<?php if (isset($title_suffix['contextual_links'])): ?>
  <?php print render($title_suffix['contextual_links']); ?>
<?php endif; ?>

<div class="statistic">
  <div class="group-statistic">
    <?php print render($content['field_statistic_value']); ?>
    <?php print render($content['field_statistic_value_text']); ?>
    <div class="group-trend">
      <?php print render($content['field_statistic_trend_show_icon']); ?>
      <?php print render($content['field_statistic_trend_text']); ?>
    </div>
  </div>
</div>
</<?php print $ds_content_wrapper ?>>

<?php if (!empty($drupal_render_children)): ?>
  <?php print $drupal_render_children ?>
<?php endif; ?>
