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
  
  $content_class = 'content-full';
  if ($sidebar_first || $sidebar_second):
    $content_class = 'content';
  endif;
?>
<div class="page">
  <header class="header header--light" role="banner">
    <div class="container">
      <div class="row">
        <div class="header__logo">
          <?php if ($logo): ?>
            <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" class="logo">
              <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" class="logo__image" />
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
        <div class="header__search">
          <?php print render($page['header']); ?>
        </div>
      </div>
    </div>
  </header>

  <?php print render($page['alerts']); ?>

  <?php print render($page['navigation']); ?>

  <?php print render($page['highlighted']); ?>

  <div class="page-content">

    <?php if(!drupal_is_front_page()): ?>
      <!-- Show the title and breadcrumbs when they are not on the homepage -->
      <div class="page-title header header--light">
        <div class="container">
          <div class="row">
            <div class="page-title__core">
              <?php print $breadcrumb; ?>

              <?php if ($title && !isset($section_title)): ?>
                <?php print render($title_prefix); ?>
                <h1 class="uikit-header-heading"><?php print $title; ?></h1>
                <?php print render($title_suffix); ?>
              <?php endif; ?>

              <?php if (isset($section_title)): ?>
                <p class="section-header"><?php print $section_title; ?></p>
              <?php endif; ?>

              <?php print render($page['title']); ?>

            </div>
            <div class="page-title__supp">
              <?php print render($page['sub_title']); ?>
            </div>
          </div>
          <div id="readspeaker">
            <readspeaker></readspeaker>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php print render($page['content_top']); ?>

    <div class="page-layout">
      <div class="row">

        <?php if ($sidebar_first): ?>
          <aside class="sidebar sidebar-left" role="complementary">
            <?php print $sidebar_first; ?>
          </aside>
        <?php endif; ?>

        <main class="<?php print $content_class; ?>" id="content" role="main">
          <?php print $messages; ?>
          <?php print render($tabs); ?>
          <?php print render($page['help']); ?>
          <?php if ($action_links): ?>
            <ul class="action-links"><?php print render($action_links); ?></ul>
          <?php endif; ?>

          <?php if (isset($section_title)): ?>
            <?php print render($title_prefix); ?>
            <h1><?php print $title; ?></h1>
            <?php print render($title_suffix); ?>
          <?php endif; ?>

          <?php print render($page['content']); ?>
          <?php print $feed_icons; ?>
        </main>

        <?php if ($sidebar_second): ?>
          <aside class="sidebar sidebar-right" role="complementary">
            <?php print $sidebar_second; ?>
          </aside>
        <?php endif; ?>
      </div>
    </div>

    <?php print render($page['content_bottom']); ?>
    
    <?php print render($page['featured']); ?>

  </div>

  <footer class="footer <?php print $classes; ?>" role="contentinfo">
    <div class="container">
      <div class="footer__navigation row">
        <?php print render($page['footer_top']); ?>
      </div>
      <div class="footer__end row">
        <?php print render($page['footer_bottom']); ?>
      </div>
      <div class="row">
        <div class="footer__logo">
          <img src="/<?php print path_to_theme(); ?>/images/coat-of-arms.png" alt="Commonwealth Coat of Arms crest logo">
        </div>
        <p class="footer__attribution">
          <small>© Commonwealth of Australia, <a href="https://raw.githubusercontent.com/govau/uikit/master/LICENSE" rel="external license">MIT licensed</a></small>
        </p>
      </div>
    </div>
  </footer>
  <div id="backtotop">
    <backtotop></backtotop>
  </div>
</div>
