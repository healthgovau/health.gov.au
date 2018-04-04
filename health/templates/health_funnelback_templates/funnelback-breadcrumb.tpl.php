<?php
/**
 * @file
 * Fannelback breadcrumb template.
 */
?>
<div id="edit-custom-remove" class="form-item form-type-item">
  <?php if ($selected): ?>
      <label for="edit-custom-remove">Filters applied:</label>
      <div class="facet-remove">
        <?php foreach($facets as $facet):
          if (!empty($facet['selectedValues'])): ?>
            <?php foreach($facet['selectedValues'] as $selectedValue): ?>
                  <a class="facet-remove-link" href="<?php print FunnelbackQueryString::funnelbackFilterSystemQueryString($selectedValue['toggleUrl']); ?>" title="Remove <?php print $selectedValue['label']; ?>">
                    <?php print $selectedValue['label']; ?>
                      <i class="fa fa-times-circle" aria-hidden="true"></i>
                  </a>
            <?php endforeach; ?>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
      <a class="clear-all" href="<?php print FunnelbackQueryString::funnelbackFilterSystemQueryString($facet_extras['unselectAllFacetsUrl']); ?>" title="Remove all refinements">
          Clear all
      </a>
  <?php endif; ?>
</div>
