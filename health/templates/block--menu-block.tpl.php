<?php
/**
 * @file
 * Returns the HTML for a block.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728246
 */
?>
<div class="<?php print $classes; ?>"<?php print $attributes; ?> id="<?php print $block_html_id; ?>">

    <a class="mobile-nav-toggle" href="#">
        <span class="local-nav-title">In this section</span>
    </a>

  <?php print $content; ?>
</div>
