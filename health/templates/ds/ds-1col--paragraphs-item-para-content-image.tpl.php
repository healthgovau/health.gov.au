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

<?php $id = _uuid_generate_php(); ?>

<div class="health-figure standard-gap <?php print array_key_exists('field_image_caption', $content) ? 'health-figure--border' : '' ?>">
  <p class="au-display-sm"><?php print render($content['field_title'][0]); ?></p>
  <figure role="group" class="standard-gap <?php print array_key_exists('field_image_caption', $content) ? 'au-accordion' : '' ?>">
    <?php print render($content['field_image']);?>

    <div class="text--minor standard-gap"><?php print render($content['field_table_source'][0])?></div>

    <?php if (array_key_exists('field_image_caption', $content)): ?>

    <a href="#<?php print $id ?>" class="standard-gap au-accordion__title au-accordion--closed js-au-accordion" aria-controls="<?php print $id ?>" aria-expanded="false" aria-selected="true" role="tab" onclick="return AU.accordion.Toggle( this )">
      Show description of image
    </a>

    <figcaption class="au-accordion__body au-accordion--closed" id="<?php print $id ?>">
      <div class="au-accordion__body-wrapper">
        <?php print render($content['field_image_caption'][0]);?>
      </div>
    </figcaption>

    <?php endif; ?>

  </figure>

</div>
