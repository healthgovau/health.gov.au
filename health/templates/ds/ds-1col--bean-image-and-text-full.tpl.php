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
<article class="col-xs-12 <?php print $classes; ?> clearfix"<?php print $attributes; ?>>

    <a class="au-card au-card--shadow au-cta-link" href="<?php print $wrapper_link_url; ?>">
        <?php if (isset($content['field_image'])): ?>
          <div class="au-card__image au-card__fullwidth">
            <?php print render($content['field_image']); ?>
          </div>
        <?php endif; ?>
        <?php if (isset($content['field_link_internal']['#items'][0])): ?>
          <h3 class="au-card__title">
            <?php print render($content['field_link_internal']['#items'][0]['title']); ?>
          </h3>
        <?php endif; ?>
        <?php if (isset($content['field_bean_text'])): ?>
          <div class="au-card__text">
            <?php print render($content['field_bean_text']); ?>
          </div>
        <?php endif; ?>
    </a>

</article>