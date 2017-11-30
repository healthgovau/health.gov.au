<div class="block block-bean contextual-links-region first last odd block-bean-call-to-action" id="block-bean-professionals-hub-additional-inf">
    <?php if ($title): ?>
  <h3 class="block__title"><?php print $title; ?></h3>
    <?php endif; ?>
  <div class="entity entity-bean bean-call-to-action clearfix" typeof="">
    <div class="bean-block-content">
        <?php if($text): ?>
      <div class="field field-name-field-bean-body field-type-text-long field-label-hidden">
          <div class="field-items">
              <div class="field-item even">
                  <p><?php print $text; ?></p>
              </div>
          </div>
      </div>
        <?php endif; ?>
        <div class="field field-name-field-link-to field-type-link-field field-label-hidden">
            <div class="field-items">
                <div class="field-item even">
                    <?php print $link; ?>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>