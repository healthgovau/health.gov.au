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
                    <div class="mobile-toggle mobile-toggle__local-nav">
                        <a href="#" class="mobile-toggle__open toc-filter-processed">
                            In this section <i class="fas fa-angle-down" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="mobile-toggle__close toc-filter-processed">
                            Close <i class="fas fa-angle-up" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="<?php print $classes; ?>"<?php print $attributes; ?> id="<?php print $block_html_id; ?>">

  <?php if ($block->region != 'sidebar_second') : ?>
    <?php print render($title_prefix); ?>
    <?php if ($title): ?>
      <h3<?php print $title_attributes; ?>><?php print $title; ?></h3>
    <?php endif; ?>
    <?php print render($title_suffix); ?>
  <?php endif; ?>

  <?php print $content; ?>
</div>
