<div class="block block-bean block-bean-call-to-action">
  <?php if ($title): ?>
  <h3 class="block__title uikit-cta-link">
    <a href="<?php print $url; ?>">
      <?php print $title; ?>
    </a>
  </h3>
  <?php endif; ?>
  <div class="entity entity-bean bean-call-to-action clearfix" typeof="">
    <div class="bean-block-content">
      <?php if($text): ?>
      <p>
        <?php print $text; ?>
      </p>
      <?php endif; ?>
    </div>
  </div>
</div>