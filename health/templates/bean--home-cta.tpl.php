<?php
/**
 * @file
 * Default theme implementation for beans.
 *
 * Available variables:
 * - $content: An array of comment items. Use render($content) to print them all, or
 *   print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $title: The (sanitized) entity label.
 * - $url: Direct url of the current entity if specified.
 * - $page: Flag for the full page state.
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. By default the following classes are available, where
 *   the parts enclosed by {} are replaced by the appropriate values:
 *   - entity-{ENTITY_TYPE}
 *   - {ENTITY_TYPE}-{BUNDLE}
 *
 * Other variables:
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 *
 * @see template_preprocess()
 * @see template_preprocess_entity()
 * @see template_process()
 */
?>

<div class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>
  <?php if(isset($content['field_feature_image'])): ?>
    <?php print render($content['field_feature_image']); ?>
  <?php endif; ?>
  <?php if($title): ?>
    <h3 class="bean-title"<?php print $title_attributes; ?>>
      <a href="/<?php print render($content['field_link_to'][0]['#element']['url']); ?>">
      <?php print $title; ?>
      </a>
    </h3>
  <?php endif; ?>
  <div class="uikit-card--content"<?php print $content_attributes; ?>>
    <?php
      hide($content['field_link_to']);
      print render($content);
    ?>
  </div>
</div>

