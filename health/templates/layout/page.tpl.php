<?php
/**
 * @file
 * Returns the HTML for a single Drupal page.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/17282148
 */
?>

<?php
// Variables
$sidebar_first  = render($page['sidebar_first']);
$sidebar_second = render($page['sidebar_second']);

$content_class = 'main-content-full';
if ($sidebar_first || $sidebar_second):
$content_class = 'main-content';
endif;
?>

<div class="nav-overlay"></div>

<div class="page au-body">
  <div class="header-top">
    <div class="container">
      <?php print render($page['header-top']); ?>
    </div>
  </div>
  <header class="au-header au-header--light" role="banner">
    <div class="container">
      <div class="row">
        <div class="col-sm-6">
          <?php if ($logo): ?>
            <a href="<?php print $front_page; ?>"
             title="<?php print t('Home'); ?>" rel="home"
             class="au-header__logo">
              <img data-src="/<?php print path_to_theme(); ?>/images/DoHCrest.png"
                alt="<?php print t('Australia government Department of Health'); ?>"
                class="au-header__logo-image au-responsive-media-img"/>
            </a>
          <?php endif; ?>
          <?php if ($site_name || $site_slogan): ?>
            <div class="element-invisible header__header-subline">
            <?php if ($site_name): ?>
              <p><?php print $site_name; ?></p>
            <?php endif; ?>

            <?php if ($site_slogan): ?>
              <div><?php print $site_slogan; ?></div>
            <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
        <div class="col-sm-6">
          <?php print render($page['header']); ?>
        </div>
      </div>
    </div>
  </header>

  <?php print render($page['alerts']); ?>

  <?php print render($page['navigation']); ?>

  <?php print $health_alert_bar; ?>

  <?php print render($page['highlighted']); ?>

  <div class="page-content" id="page-content">

  <?php if(!drupal_is_front_page()): ?>
    <div class="page-title header header--light">
    <div class="container">
      <div class="row">
      <div class="page-title__core col<?php print isset($section_title) ? ' section-title' : '';
      print count($page['title_supp']) > 0 || isset($header_image) ? '': ' full'; ?>">
        <?php print $breadcrumb; ?>

        <?php print render($page['title_core']); ?>

        <?php if (isset($section_title)): ?>
          <div class="section-header rs_skip <?php print count($page['title_supp']) > 0 ? '' : 'full'; ?>">
            <?php print $section_title; ?>
          </div>
        <?php endif; ?>

        <?php if ($title): ?>
        <?php print render($title_prefix); ?>
        <h1 class="au-header-heading">
          <?php print $title; ?>
        </h1>
        <?php print render($title_suffix); ?>
        <?php endif; ?>

        <?php if (isset($summary)): ?>
        <p class="summary"><?php print $summary; ?></p>
        <?php endif; ?>

      </div>

      <?php if (count($page['title_supp']) > 0 || isset($header_image)): ?>
        <div class="page-title__supp col <?php print isset($section_title) ? 'section-title' : '' ?>">
          <?php print render($page['title_supp']); ?>
          <?php print isset($header_image) ? $header_image : ''; ?>
        </div>
      <?php endif; ?>
      </div>
    </div>
    </div>
  <?php endif; ?>

  <?php print render($page['content_top']); ?>

  <div class="page-layout">
    <div class="row">

    <?php if ($sidebar_first): ?>
      <aside class="sidebar sidebar-left rs_skip" role="complementary">
      <?php print $sidebar_first; ?>
      </aside>
    <?php endif; ?>

    <?php if ($sidebar_second): ?>
      <aside class="sidebar sidebar-right rs_skip" role="complementary">
        <?php print $sidebar_second; ?>
      </aside>
    <?php endif; ?>

    <main class="<?php print $content_class; ?>" id="content" role="main">
      <div id="tabs" class="rs_skip"><?php print render($tabs); ?></div>
      <?php print render($page['help']); ?>
      <?php if ($action_links): ?>
        <ul class="action-links rs_skip"><?php print render($action_links); ?></ul>
      <?php endif; ?>

      <?php print $messages; ?>

      <?php print $readspeaker; ?>

      <?php print render($page['content']); ?>
      <?php print $feed_icons; ?>
    </main>

    </div>
  </div>

  <?php print render($page['content_bottom']); ?>

  <?php print render($page['featured']); ?>

  </div>

  <footer class="au-footer health-footer <?php print $classes; ?>" role="contentinfo">
    <div class="container">
      <div class="au-footer__navigation health-footer__navigation row">
      <?php print render($page['footer_top']); ?>
      </div>
      <div class="au-footer__end health-footer__end row">
      <?php print render($page['footer_bottom']); ?>
        <div class="au-footer__logo health-footer__logo">
          <div class="image-wrapper image-loading rs_preserve rs_skip" style="padding-bottom: 73%">
            <div class="image">
              <img typeof="foaf:Image" width="201" height="147"
                   alt="Commonwealth Coat of Arms crest logo"
                   data-src="/<?php print path_to_theme(); ?>/images/GovCrest.png">
            </div>
          </div>
        </div>
        <p class="au-footer__attribution health-footer__attribution">
          <small>© Commonwealth of Australia</small>
        </p>
      </div>
    </div>
  </footer>
  <?php print $backtotop; ?>
</div>
