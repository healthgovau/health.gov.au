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
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700"
        rel="stylesheet">
  <title><?php print $head_title; ?></title>

  <?php if ($default_mobile_metatags): ?>
    <meta name="MobileOptimized" content="width">
    <meta name="HandheldFriendly" content="true">
    <meta name="viewport" content="width=device-width">
  <?php endif; ?>
  <script type="text/javascript">
    var $html = document.documentElement;
    if ($html.classList) $html.classList.remove("no-js"), $html.classList.add("js"); else {
      var className = "no-js";
      $html.className = $html.className.replace(new RegExp("(^|\\b)" + className.split(" ").join("|") + "(\\b|$)", "gi"), " "), $html.className += " js"
    }
  </script>
  <?php if ($add_html5_shim): ?>
    <!--[if lt IE 9]>
    <script
      src="<?php print $base_path . $path_to_health; ?>/js/html5shiv.min.js"></script>
    <![endif]-->
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

  <!-- BEGIN Optimal Workshop Intercept Snippet -->
  <div id='owInviteSnippet' style='position:fixed;right:20px;bottom:20px;width:280px;padding:20px;margin:0;border-radius:6px;background:#008998;color:#FAF5F5;text-align:left;z-index:2200000000;opacity:0;transition:opacity 500ms;-webkit-transition:opacity 500ms;display:none;'><div id='owInviteMessage' style='padding:0;margin:0 0 20px 0;font-size:16px;'>Please help us improve this website by taking a short 5 minute survey.</div><a id='owInviteOk' href='https://health-transformation.optimalworkshop.com/questions/j0373s8u?tag=intercept&utm_medium=intercept' target='_blank' style='color:#FFFFFF;font-size:16px;font-weight:bold;text-decoration:underline;'>Take a survey</a><a id='owInviteCancel' href='javascript:void(0)' onclick='this.parentNode.style.display="none";' style='color:#FAF5F5;font-size:14px;text-decoration:underline;float:right;'>No thanks</a></div><script>var owOnload=function(){if(-1==document.cookie.indexOf('owInvite')){var o=new XMLHttpRequest;o.onloadend=function(){try{var o=document.getElementById('owInviteSnippet');this.response&&JSON.parse(this.response).active===!0&&(document.cookie='owInvite=Done',setTimeout(function(){o.style.display='block',o.style.opacity=1},2e3))}catch(e){}},o.open('POST','https://www.optimalworkshop.com/survey_status/questions/xj228804/active'),o.send()}};if(window.addEventListener){window.addEventListener('load',function(){owOnload();});}else if(window.attachEvent){window.attachEvent('onload',function(){owOnload();});}</script>
  <!-- END Optimal Workshop snippet -->
</body>
</html>
