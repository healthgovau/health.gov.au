<?php
/**
 * @file
 * Main page template.
 */
?>
<?php drupal_add_js('https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js'); ?>
<?php drupal_add_js(path_to_theme() . '/js/media-manager/library.js', 'file'); ?>

<div id="content" class="clearfix">
  <div id="content-wrapper">
    <div id="main-content">
      <?php print render($page['content']); ?>
    </div>
  </div>
</div>
