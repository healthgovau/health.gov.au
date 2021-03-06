<?php

/**
 * @file
 * Display Suite 1 column template.
 */
?>
<article class="col-sm-6 col-lg-3">
  <a class="au-card <?php print $card_style; ?>" href="<?php print($content['field_link_internal']['#items'][0]['url']); ?>">
    <?php if (isset($content['field_link_internal']['#items'][0])): ?>
      <h3 class="au-card__title"><?php print($content['field_link_internal']['#items'][0]['title']); ?></h3>
    <?php endif; ?>
    <?php if (isset($content['field_summary']['#items'][0])): ?>
      <div class="au-card__text"><?php print($content['field_summary']['#items'][0]['value']); ?></div>
    <?php endif; ?>
  </a>
</article>
