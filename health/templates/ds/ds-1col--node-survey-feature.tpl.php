<?php

/**
 * @file
 * Display Suite 1 column template.
 */
?>
<<?php print $ds_content_wrapper; print $layout_attributes; ?> class="ds-1col <?php print $classes;?> clearfix">

  <?php if (isset($title_suffix['contextual_links'])): ?>
  <?php print render($title_suffix['contextual_links']); ?>
  <?php endif; ?>


  <a href="<?php print url('node/' . $node->nid)?>" class="au-card au-card--shadow">
    <div class="au-card__image au-card__fullwidth">
      <?php print render($content['field_image_featured']); ?>
    </div>
    <h3 class="au-card__title"><?php print $title?></h3>

    <div class="au-card__text">
      <?php print render($content['field_status']); ?>
      <?php print render($content['field_time_required']); ?>
      <?php print render($content['field_summary']); ?>
    </div>
  </a>

</<?php print $ds_content_wrapper ?>>

<?php if (!empty($drupal_render_children)): ?>
  <?php print $drupal_render_children ?>
<?php endif; ?>
