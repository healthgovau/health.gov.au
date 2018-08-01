<div class="block block-bean block-bean-call-to-action">
  <?php if ($title): ?>
    <a href="<?php print $url; ?>"><?php print $title; ?></a>
  <?php endif; ?>
  <div class="entity entity-bean clearfix" typeof="">
    <div class="bean-block-content">
      <?php if($text): ?>
      <p>
        <?php print $text; ?>
      </p>
      <?php endif; ?>
    </div>
  </div>
</div>