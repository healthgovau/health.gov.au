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

<body class="<?php print $classes; ?>" <?php print $attributes;?>>
  <?php print $google_tag_manager; ?>
  <nav class="skip-link" id="skip-link">
    <a class="skip-link__link" href="#content">Skip to main content</a>
    <a class="skip-link__link" href="#nav">Skip to main navigation</a>
  </nav>

  <?php print $page_top; ?>
  <?php print $page; ?>
  <?php print $page_bottom; ?>

</body>
</html>
