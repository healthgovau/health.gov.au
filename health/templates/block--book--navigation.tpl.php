<?php
/**
 * @file
 * Returns the HTML for a block.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728246
 */
?>

<?php
if (user_is_logged_in()) {
  $node = menu_get_object('node');
  $link =  l('Edit structure', 'admin/content/book/' . $node->book['bid'], ['query' => ['destination' => current_path()]]);
?>
  <div id="tabs" class="rs_skip">
    <h2 class="element-invisible">Primary tabs</h2>
    <ul class="tabs primary">
      <li><?php print $link ?></li>
    </ul>
  </div>
<?php } ?>


<div class="health-accordion">
  <a href="#" class="localnav__mobile-toggle health-accordion__title health-accordion--closed" aria-controls="<?php print $block_html_id; ?>">
    <span>In this section</span>
  </a>
</div>
<div class="<?php print $classes; ?> health-accordion health-accordion__body"<?php print $attributes; ?> id="<?php print $block_html_id; ?>">

  <?php print render($title_prefix); ?>
  <?php if ($title): ?>
    <h3<?php print $title_attributes; ?>><?php print $title; ?></h3>
  <?php endif; ?>
  <?php print render($title_suffix); ?>

  <?php print $content; ?>
</div>
