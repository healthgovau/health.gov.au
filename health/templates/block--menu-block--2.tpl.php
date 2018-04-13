<?php
/**
 * @file
 * Returns the HTML for a block.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728246
 */
?>
<div class="block" id="block-bean-local-navigation-menu-toggle-for">
    <div class="entity entity-bean bean-basic-content local-navigation-menu-toggle-for clearfix" about="/block/local-navigation-menu-toggle-for" typeof="">
        <div class="field field-name-field-bean-body field-type-text-long field-label-hidden">
            <div class="field-items">
                <div class="field-item even">
                    <div class="mobile-toggle mobile-toggle__local-nav health-accordion">
                        <a href="#" class="mobile-toggle__open toc-filter-processed health-accordion__title health_accordion--closed"  aria-controls="block-menu-block-2">
                          <span>In this section</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="<?php print $classes; ?> health-accordion health-accordion__body health-accordion--closed"<?php print $attributes; ?> id="<?php print $block_html_id; ?>">

  <?php if ($block->region != 'sidebar_second') : ?>
    <?php print render($title_prefix); ?>
    <?php if ($title): ?>
      <h3<?php print $title_attributes; ?>><?php print $title; ?></h3>
    <?php endif; ?>
    <?php print render($title_suffix); ?>
  <?php endif; ?>

  <?php print $content; ?>
</div>
