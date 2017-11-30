<?php
/**
 * @file
 * Returns the HTML for a single Drupal page while offline.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728174
 */
?><!DOCTYPE html>
<html <?php print $html_attributes; ?>>
<head>
  <?php print $head; ?>
  <title><?php print $head_title; ?></title>

  <?php if ($default_mobile_metatags): ?>
    <meta name="MobileOptimized" content="width">
    <meta name="HandheldFriendly" content="true">
    <meta name="viewport" content="width=device-width">
  <?php endif; ?>

  <?php print $styles; ?>
  <?php print $scripts; ?>
  <?php if ($add_html5_shim): ?>
    <!--[if lt IE 9]>
    <script src="<?php print $base_path . $path_to_health; ?>/js/html5.js"></script>
    <![endif]-->
  <?php endif; ?>
</head>
<body class="<?php print $classes; ?>" <?php print $attributes;?>>

<div class="uikit-body uikit-grid">

  <header class="uikit-header" role="banner">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <?php if ($logo): ?>
            <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" class="header__logo">
              <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" class="header__logo-image uikit-responsive-media-img" />
            </a>
          <?php endif; ?>
          <?php if ($site_name || $site_slogan): ?>
            <div class="element-invisible header__header-subline">
              <?php if ($site_name): ?>
                <h1><?php print $site_name; ?></h1>
              <?php endif; ?>

              <?php if ($site_slogan): ?>
                <div><?php print $site_slogan; ?></div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
            <?php print render($page['header']); ?>
        </div>
      </div>
    </div>
  </header>

  
  <div class="page-layout container">

    <div class="main-content" role="main">
      <?php print $highlighted; ?>
      <a id="main-content"></a>
      <?php if ($title): ?>
        <h1><?php print $title; ?></h1>
      <?php endif; ?>
      <?php print $messages; ?>
      <?php print $content; ?>
    </div>

    <div class="main-navigation">
      <?php print $navigation; ?>
    </div>

    <?php if ($sidebar_first || $sidebar_second): ?>
      <aside class="sidebars">
        <?php print $sidebar_first; ?>
        <?php print $sidebar_second; ?>
      </aside>
    <?php endif; ?>

  </div>

  <?php print $footer; ?>

</div>

<?php print $bottom; ?>

</body>
</html>
