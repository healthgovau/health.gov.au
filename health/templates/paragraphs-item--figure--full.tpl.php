<?php

/**
 * @file
 * Default theme implementation for a single paragraph item.
 *
 * Available variables:
 * - $content: An array of content items. Use render($content) to print them
 *   all, or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. By default the following classes are available, where
 *   the parts enclosed by {} are replaced by the appropriate values:
 *   - entity
 *   - entity-paragraphs-item
 *   - paragraphs-item-{bundle}
 *
 * Other variables:
 * - $classes_array: Array of html class attribute values. It is flattened into
 *   a string within the variable $classes.
 *
 * @see template_preprocess()
 * @see template_preprocess_entity()
 * @see template_process()
 */
?>
<?php print render($content['field_title']);?>
<div class="figure__wrapper">
<figure>
  <a href="<?php print image_style_url('full', $content['field_figure'][0]['#item']['uri'])?>">
    <?php print render($content['field_figure']);?>
  </a>
  <figcaption>
    <p><?php print render($content['field_image_long_description'][0]['#markup']);?></p>
  </figcaption>

</figure>
<?php print render($content['field_table_source'])?>
</div>