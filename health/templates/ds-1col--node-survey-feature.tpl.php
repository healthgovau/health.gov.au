<?php

/**
 * @file
 * Display Suite 1 column template.
 */
?>
<<?php print $ds_content_wrapper; print $layout_attributes; ?> class="ds-1col <?php print $classes;?> clearfix">

  <div class="wrapper">

    <?php if (isset($title_suffix['contextual_links'])): ?>
    <?php print render($title_suffix['contextual_links']); ?>
    <?php endif; ?>

    <?php print $ds_content; ?>

    <div class="survey-button">
      <a href="/<?php print drupal_get_path_alias('node/' . $node->nid)?>" class="button">Participate</a>
    </div>

  </div>
</<?php print $ds_content_wrapper ?>>

<?php if (!empty($drupal_render_children)): ?>
  <?php print $drupal_render_children ?>
<?php endif; ?>