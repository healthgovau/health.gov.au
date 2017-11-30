<?php
/**
 * @file
 * Returns the HTML for a block.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728246
 */
?>
<div class="<?php print $classes; ?>"<?php print $attributes; ?> id="<?php print $block_html_id; ?>">
    <?php print render($title_prefix); ?>
    <?php if ($title): ?>
    <a href="<?php print '#accordion-' . $block_html_id?>"
       class="uikit-accordion__title uikit-accordion--closed js-uikit-accordion"
       aria-controls="<?php print 'accordion-' . $block_html_id; ?>"
       aria-expanded="false"
       aria-selected="false"
       role="tab"
       onClick="return UIKIT.accordion.Toggle( this )">
       <h2<?php print $title_attributes; ?>><?php print $title; ?></h2></a>
    <?php endif; ?>
    <?php print render($title_suffix); ?>

    <div class="uikit-accordion__body uikit-accordion--closed" id="<?php print 'accordion-' . $block_html_id; ?>" aria-hidden="true">
        <div class="uikit-accordion__body-wrapper">
            <?php print $content; ?>
        </div>
    </div>
</div>
