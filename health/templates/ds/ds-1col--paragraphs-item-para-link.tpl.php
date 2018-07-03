<?php

/**
 * @file
 * Display Suite 1 column template.
 */
?>
<article class="col-xs-12 col-sm-3">
    <a class="au-card au-card--shadow au-cta-link" href="<?php print($content['field_link_internal']['#items'][0]['url']); ?>">
        <h3 class="au-card__title"><?php print($content['field_link_internal']['#items'][0]['title']); ?></h3>
        <div class="au-card__text"><?php print($content['field_summary']['#items'][0]['value']); ?>
        </div>
    </a>
</article>

