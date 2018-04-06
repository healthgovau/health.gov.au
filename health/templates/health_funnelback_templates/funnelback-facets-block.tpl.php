<?php
/**
 * @file
 * Facet template for Funnelback.
 */

if (!empty($facets)):
  foreach($facets as $key => $facet): ?>
      <div class='facet block-facetapi'>
          <div class='facet-header'>
              <a href="<?php print '#accordion-' . $key?>"
                 class="uikit-accordion__title uikit-accordion--<?php print $facet['selected'] ? 'open' : 'closed' ?> js-uikit-accordion"
                 aria-controls="<?php print 'accordion-' . $key; ?>"
                 aria-expanded="<?php print $facet['selected'] ? 'true' : 'false' ?>"
                 aria-selected="false"
                 role="tab"
                 onClick="return UIKIT.accordion.Toggle( this )">
                  <h2 class="facet-name block__title"><?php print $facet['name']; ?></h2></a>
          </div>
          <div class="uikit-accordion__body uikit-accordion--<?php print $facet['selected'] ? 'open' : 'closed' ?>" id="<?php print 'accordion-' . $key; ?>" aria-hidden="true">
              <div class="uikit-accordion__body-wrapper">
                  <ul class="facetapi-facetapi-checkbox-links">
                    <?php if (!empty($facet['categories'])): ?>
                      <?php foreach($facet['categories'] as $category): ?>
                        <?php foreach($category['values'] as $value): ?>
                                <li>
                                  <?php switch($facet['guessedDisplayType']):
                                    case 'CHECKBOX': ?>
                                        <input type="checkbox" class="facet-checkbox" <?php print $value['selected'] == TRUE ? 'checked' : ''; ?> redirect="<?php print FunnelbackQueryString::funnelbackFilterSystemQueryString($value['toggleUrl']); ?>">
                                      <?php break; ?>
                                    <?php case 'RADIO_BUTTON': ?>
                                          <input type="radio" class="facet-radio" <?php print $value['selected'] == TRUE ? 'checked' : ''; ?> redirect="<?php print FunnelbackQueryString::funnelbackFilterSystemQueryString($value['toggleUrl']); ?>">
                                      <?php break; ?>
                                    <?php case 'SINGLE_DRILL_DOWN': ?>
                                      <?php print $value['selected'] == TRUE ? 'x' : ''; ?>
                                      <?php break; ?>
                                    <?php endswitch; ?>
                                    <a href="<?php print FunnelbackQueryString::funnelbackFilterSystemQueryString($value['toggleUrl']); ?>"><span class="facet-item-label"><?php print $value['label'] ?></span> <span class="facet-item-count">(<?php print $value['count']; ?>)</span></a>
                                </li>
                        <?php endforeach ?>
                      <?php endforeach ?>
                    <?php else: ?>
                        <li>This filter is not available</li>
                    <?php endif; ?>
                  </ul>
              </div>
          </div>
      </div>
  <?php endforeach ?>

<?php endif; ?>
