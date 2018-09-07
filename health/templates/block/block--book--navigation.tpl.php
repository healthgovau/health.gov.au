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
  $link = l(t('Edit structure'), 'admin/content/book/' . $node->book['bid'], ['query' => ['destination' => current_path()]]);
  ?>
  <div id="tabs" class="rs_skip">
    <h2 class="element-invisible">Primary tabs</h2>
    <ul class="health-tabs au-link-list au-link-list--inline primary">
      <li><?php print $link ?></li>
    </ul>
  </div>
<?php } ?>

<aside class="au-side-nav au-accordion">
  <a
    href="#book-navigation" class="au-side-nav__toggle au-accordion__title au-accordion--closed"
    aria-controls="book-navigation"
    aria-expanded="false"
    aria-selected="false"
    role="tab"
    onclick="return AU.accordion.Toggle( this )"
  >
    In this section
  </a>
  <div id="book-navigation" class="au-side-nav__content au-accordion--closed au-accordion__body">
    <?php print render($title_prefix); ?>
    <?php if ($title): ?>
    <h2 class="au-sidenav__title">
      <?php print $title; ?>
    </h2>
    <?php endif; ?>
    <?php print render($title_suffix); ?>
    <?php print $content; ?>
  </div>
</aside>