<div class="block block-bean contextual-links-region first last odd block-bean-call-to-action rs_skip" id="block-bean-professionals-hub-additional-inf">
  <a href="<?php print $url; ?>">
    <?php if ($title): ?>
      <h3 class="block__title"><?php print $title; ?></h3>
    <?php endif; ?>
      <div class="entity entity-bean bean-call-to-action clearfix" typeof="">
          <div class="bean-block-content">
            <?php if($text): ?>
                <div class="field field-name-field-bean-text field-type-text-long field-label-hidden">
                    <div class="field-items">
                        <div class="field-item even">
                            <p><?php print $text; ?></p>
                            <p><i class="fas <?php print $font; ?>"></i><?php print $link_text; ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
          </div>
      </div>
  </a>
</div>