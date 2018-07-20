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

  <div class="bean-block-content"<?php print $content_attributes; ?>>
    <a href="<?php print $wrapper_link_url; ?>">
      <?php print render($content['field_image']); ?>
      <div class="field field-name-field-link-internal field-type-link-field field-label-hidden">
        <div class="field-items">
          <div class="field-item even">
            <?php print render($content['field_link_internal']['#items'][0]['title']); ?>
          </div>
        </div>
      </div>
      <?php print render($content['field_bean_text']); ?>
    </a>
  </div>

</div>