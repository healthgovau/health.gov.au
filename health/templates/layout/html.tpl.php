<?php

/**
 * @file
 * Returns the HTML for the basic html structure of a single Drupal page.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728208
 */
?><!DOCTYPE html>
<!--[if lt IE 8]>
<html
  class="no-js lt-ie8 lt-ie9"<?php print $html_attributes . $rdf_namespaces; ?>>
<![endif]-->
<!--[if IE 8]>
<html
  class="no-js lt-ie9 ie8"<?php print $html_attributes . $rdf_namespaces; ?>>
<![endif]-->
<!--[if IE 9 ]>
<html class="no-js ie9"<?php print $html_attributes . $rdf_namespaces; ?>>
<![endif]-->
<!--[if !(IE)]><!-->
<html class="no-js"<?php print $html_attributes . $rdf_namespaces; ?>>
<!--<![endif]-->

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
</head>

<body class="au-grid <?php print $classes; ?>" <?php print $attributes;?>>
  <?php print $google_tag_manager; ?>
  <nav class="au-skip-link">
    <a class="au-skip-link__link" href="#content">Skip to main content</a>
    <a class="au-skip-link__link" href="#nav">Skip to main navigation</a>
  </nav>

  <?php print $page_top; ?>
  <?php print $page; ?>

  <!--[if gt IE 8]><!-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <!--<![endif]-->

  <!--[if IE 8]>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
  <script src="/<?php print path_to_theme(); ?>/js/dist/script-ie8.min.js"></script>
  <![endif]-->

  <?php print $page_bottom; ?>

  <!--[if gt IE 8]><!-->
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-lazyload/8.6.0/lazyload.min.js"></script>
  <script type="text/javascript" src="/<?php print path_to_theme(); ?>/js/dist/health.lazyload.min.js"></script>
  <script type="text/javascript" src="/<?php print path_to_theme(); ?>/js/libraries/anchorific.min.js"></script>
  <script type="text/javascript" src="/<?php print path_to_theme(); ?>/js/dist/health.toc.min.js"></script>
  <script type="text/javascript" src="https://unpkg.com/tippy.js@2.5.2/dist/tippy.all.min.js"></script>
  <script type="text/javascript" src="/<?php print path_to_theme(); ?>/js/dist/health.tooltips.min.js"></script>
  <script type="text/javascript" src="/<?php print path_to_theme(); ?>/js/dist/health.accordion.min.js"></script>
  <!--<![endif]-->
</body>
</html>
