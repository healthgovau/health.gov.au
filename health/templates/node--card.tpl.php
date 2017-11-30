<?php
/**
 * @file
 * Returns the HTML for a node.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728164
 */

  if (isset($variables['view']->current_display) && $variables['view']->current_display == "page") { 
    $views_page = true;
  } else {
    $views_page = false;
  }

  if (isset($content['field_news_date'])) {
    $date = render($content['field_news_date'][0]['#markup']);
    hide($content['field_news_date']);
  } elseif (isset($content['field_event_date'])) {
    $date = render($content['field_event_date'][0]['#markup']);
    hide($content['field_event_date']);
  }

  $featured = false;

  if(isset($content['field_feature_image']) || isset($content['field_video'])) {
    $featured = true;
  }

?>
<article <?php print $attributes; ?> class="<?php print $classes; ?> uikit-card clearfix node-<?php print $node->nid; ?> <?php if ($featured): ?>uikit-card--featured-top<?php endif; ?>">

  
  <?php if ($featured): ?>
    <div class="uikit-card--featured">
      <?php if(isset($content['field_feature_image'])): print render($content['field_feature_image']); endif; ?>
      <?php if(isset($content['field_video'])): print render($content['field_video']); endif; ?>
    </div>
  <?php endif; ?>
  
  <div class="uikit-card--content">
    
    <?php if ($title_prefix || $title_suffix || $display_submitted || $unpublished || $preview || !$page && $title): ?>
      <header>
        <?php print render($title_prefix); ?>
        <h3<?php print $title_attributes; ?> class="node-title">
          <a href="<?php print $node_url; ?>"><?php print $title; ?></a>
        </h3>
        <?php print render($title_suffix); ?>
        
        <?php if ($display_submitted): ?>
          <p class="submitted">
            <?php print $user_picture; ?>
            <?php print $submitted; ?>
          </p>
        <?php endif; ?>
        <!-- Suffix moved to the top -->
        <?php if ($unpublished): ?>
          <mark class="watermark"><?php print t('Unpublished'); ?></mark>
        <?php elseif ($preview): ?>
          <mark class="watermark"><?php print t('Preview'); ?></mark>
        <?php endif; ?>
      </header>
    <?php endif; ?>

    <?php
      // We hide the comments and links now so that we can render them later.
      hide($content['comments']);
      hide($content['links']);
      print render($content);
    ?>

    <div class="uikit-card--bottom">
      <span class="uikit-card--type uikit-tags__item">
        <a href="#"><?php print $type_name; ?></a>
      </span>
      <span class="uikit-card--date">
        <?php if(isset($date)): print $date; endif; ?>
      </span>
    </div>

  </div>

  <?php print render($content['links']); ?>

  <?php print render($content['comments']); ?>

</article>
