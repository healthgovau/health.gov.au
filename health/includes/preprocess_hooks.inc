<?php

/**
 * @file
 * Preprocess hooks.
 */

// Include inc files.
include_once drupal_get_path('theme', 'health') . '/includes/helper.inc';

const READSPEAKER_URL = '//f1-oc.readspeaker.com/script/5802/webReader/webReader.js?pids=wr&amp;skin=health&amp;notools=1';
const GOOGLE_MAP_API = '//maps.googleapis.com/maps/api/staticmap';
const RESOURCES_TYPE = [
  'image',
  'publication',
  'video',
  'audio',
  'app_or_tool',
];
const THEME_PATH_TOKEN_GENERIC = '[theme-path]';
const PUBLICATION_TYPE_INFOGRAPHIC = 2176;

/**
 * Override or insert variables into the block templates.
 *
 * @param array $variables
 *   Variables to pass to the theme template.
 * @param string $hook
 *   The name of the template being rendered ("block" in this case.)
 */
function health_preprocess_block(array &$variables, $hook) {

  // Use a template with no wrapper for the page's main content.
  if ($variables['block_html_id'] == 'block-system-main') {
    $variables['theme_hook_suggestions'][] = 'block__no_wrapper';
  }

  // Classes describing the position of the block within the region.
  if ($variables['block_id'] == 1) {
    $variables['classes_array'][] = 'first';
  }
  // The last_in_region property is set in health_page_alter().
  if (isset($variables['block']->last_in_region)) {
    $variables['classes_array'][] = 'last';
  }
  $variables['classes_array'][] = $variables['block_zebra'];

  $variables['title_attributes_array']['class'][] = 'block__title';

  // Add Aria Roles via attributes.
  switch ($variables['block']->module) {
    case 'system':
      switch ($variables['block']->delta) {
        case 'main':
          // Note: the "main" role goes in the page.tpl, not here.
          break;

        case 'help':
        case 'powered-by':
          $variables['attributes_array']['role'] = 'complementary';
          break;

        default:
          // Any other "system" block is a menu block.
          $variables['attributes_array']['role'] = 'navigation';
          break;
      }
      break;

    case 'menu':
    case 'book':
    case 'menu_block':
    case 'blog':
    case 'comment':
    case 'forum':
    case 'shortcut':
      $variables['attributes_array']['role'] = 'navigation';
      break;

    case 'search':
    case 'search_api_page':
      $variables['attributes_array']['role'] = 'search';
      break;

    case 'help':
    case 'aggregator':
    case 'locale':
    case 'poll':
    case 'profile':
      $variables['attributes_array']['role'] = 'complementary';
      break;

    case 'node':
      switch ($variables['block']->delta) {
        case 'syndicate':
          $variables['attributes_array']['role'] = 'complementary';
          break;

        case 'recent':
          $variables['attributes_array']['role'] = 'navigation';
          break;
      }
      break;

    case 'user':
      switch ($variables['block']->delta) {
        case 'login':
          $variables['attributes_array']['role'] = 'form';
          break;

        case 'new':
        case 'online':
          $variables['attributes_array']['role'] = 'complementary';
          break;
      }
      break;
  }

  // Add a class to the block if it is a block menu with the title as a link.
  if ($variables['elements']['#block']->module == 'menu_block' && $variables['elements']['#config']['title_link']) {
    $variables['classes_array'][] = 'block-menu-block--title-link';
  }

  // Search forms.
  if ($variables['block']->module == 'search_api_page') {
    if ($variables['block']->context == 'global__mobile') {
      $variables['classes_array'][] = 'au-search--global-mobile';
    }
    else {
      $variables['classes_array'][] = 'au-search--global-desktop';
    }
  }
}

/**
 * Override or insert variables for the breadcrumb theme function.
 *
 * @param array $variables
 *   An array of variables to pass to the theme function.
 * @param string $hook
 *   The name of the theme hook being called ("breadcrumb" in this case).
 *
 * @see govcms_parkes_breadcrumb()
 */
function health_preprocess_breadcrumb(array &$variables, $hook) {
  // Provide a navigational heading to give context for breadcrumb links to
  // screen-reader users.
  if (empty($variables['title'])) {
    $variables['title'] = t('You are here');
  }
}

/**
 * Override or insert variables into entity templates.
 *
 * @param array $variables
 *   Preprocess variables.
 */
function health_preprocess_entity(array &$variables) {
  // Paragraphs.
  if ($variables['elements']['#entity_type'] == 'paragraphs_item') {

    $para_item = $variables['paragraphs_item'];

    // Add the number of child paragraph items in the listing as a css class.
    // This assumes there is only one child paragraph listing in a paragraph.
    foreach ($variables['content'] as $field => $data) {
      if ($data['#field_type'] == 'paragraphs' || $data['#field_type'] == 'entityreference') {
        $variables['classes_array'][] = 'listing__count--' . count($data['#items']);

      }
    }

    // Add bundle title to class.
    if (isset($para_item->field_pbundle_title[LANGUAGE_NONE][0])) {
      $title = $para_item->field_pbundle_title[LANGUAGE_NONE][0]['value'];
      $variables['classes_array'][] = strtolower(str_replace(' ', '-', $title));
    }

    // Add band background colour.
    if (property_exists($para_item, 'field_band_background_colour')) {
      if (!empty($para_item->field_band_background_colour)) {
        $colour = taxonomy_term_load($para_item->field_band_background_colour[LANGUAGE_NONE][0]['tid']);
        $variables['classes_array'][] = $colour->description;
      }
    }

    // Links.
    if ($para_item->bundle == 'para_links') {
      // Pass band style to link paragraph.
      $band_style = $para_item->field_band_style[LANGUAGE_NONE][0]['value'];
      foreach ($variables['content']['field_link_paragraph'] as $key => $link_entity) {
        if (is_int($key)) {
          $link_entity_id = $para_item->field_link_paragraph[LANGUAGE_NONE][$key]['value'];
          $variables['content']['field_link_paragraph'][$key]['entity']['paragraphs_item'][$link_entity_id]['#entity']->card_style = $band_style;
        }
      }
    }

    // Link.
    if ($para_item->bundle == 'para_link') {
      $variables['card_style'] = '';
      if (property_exists($para_item, 'card_style')) {
        $variables['card_style'] = $para_item->card_style;
      }
    }

    // Contact.
    if ($para_item->bundle == 'para_contact') {
      // Pass the more link to related contact.
      $nid = $para_item->field_related_contact[LANGUAGE_NONE][0]['target_id'];
      if (isset($para_item->field_link_internal[LANGUAGE_NONE][0])) {
        $link = field_view_field('paragraphs_item', $para_item, 'field_link_internal', 'full');
        $variables['content']['field_related_contact'][0]['node'][$nid]['field_summary']['#suffix'] = render($link);
      }
    }

    // Feature content.
    if ($para_item->bundle == 'para_feature_content') {
      if (isset($para_item->field_grid[LANGUAGE_NONE])) {
        $grid = $para_item->field_grid[LANGUAGE_NONE][0]['value'];
        $nid = $para_item->field_feature_content[LANGUAGE_NONE][0]['target_id'];
        // Pass grid to node.
        $grid_map = [
          'col-sm-6' => 'col-sm-6',
          'col-sm-5' => 'col-sm-7',
          'col-sm-4' => 'col-sm-8',
        ];
        $variables['content']['field_feature_content'][0]['node'][$nid]['#node']->left_grid = 'col-xs-12 ' . $grid;
        $variables['content']['field_feature_content'][0]['node'][$nid]['#node']->right_grid = $grid_map[$grid];
      }
    }

    // Statistics - Band.
    if ($para_item->bundle == 'para_statistics') {
      // Apply col classes to children.
      $variables = _health_total_children($variables, 'field_statistics');
    }

    // Statistics - Inline reference.
    if ($para_item->bundle == 'reference_statistic') {
      // Apply col classes to children.
      $variables = _health_total_children($variables, 'field_reference_statistic');
    }

    // Taxonomy term.
    if ($para_item->bundle == 'para_taxonomy') {
      if (isset($para_item->field_related_term[LANGUAGE_NONE])) {
        $term_name = $para_item->field_related_term[LANGUAGE_NONE][0]['entity']->name;
        $variables['term_name'] = $term_name;
      }
    }

    // Views.
    if ($para_item->bundle == 'para_view' || $para_item->bundle == 'view') {

      // Get the view values.
      $view_name = $para_item->field_view_name[LANGUAGE_NONE][0]['value'];
      $view_mode = $para_item->field_view_mode[LANGUAGE_NONE][0]['value'];

      // Load the view.
      $view = views_get_view($view_name);
      $view->get_total_rows = TRUE;
      $view->set_display($view_mode);
      $view->preview = TRUE;

      // Remove featured items from listings.
      if ($view_name == 'news_and_events') {
        switch ($view_mode) {
          case "latest_news_events_public":
            if ($nid = _health_get_featured_item('featured_news_events')) {
              $view->set_arguments([NULL, $nid]);
            }
            break;

          case "latest_news_events_hp":
            if ($nid = _health_get_featured_item('featured_news_events_hp')) {
              $view->set_arguments([NULL, $nid]);
            }
            break;

          case "latest_news_events_global":
            if ($nid = _health_get_featured_item('featured_news_events')) {
              $view->set_arguments([$nid]);
            }
            break;

          case "latest_news_events_public_program":
            if ($nid = _health_get_featured_item('featured_news_events')) {
              $view->set_arguments([NULL, $nid]);
            }
            break;
        }
      }
      if ($view_name == 'latest_rsources') {
        switch ($view_mode) {
          case "resources_for_public_in_hp":
          case "latest_resources_public":
          case "latest_resources_public_program":
            if ($nid = _health_get_featured_item('featured_resource')) {
              $view->set_arguments([NULL, $nid]);
            }
            break;

          case "global_public":
            if ($nid = _health_get_featured_item('featured_resource')) {
              $view->set_arguments([$nid]);
            }
            break;

          case "latest_resources_hp":
            if ($nid = _health_get_featured_item('featured_resource_hp')) {
              $view->set_arguments([NULL, $nid]);
            }
            break;
        }
      }

      // Generate the view.
      if ($view_name == 'generic_view') {

        $content_type = $related_topics = $audience = $related_initiative = 'all';

        // Content type filter.
        if (isset($para_item->field_content_type[LANGUAGE_NONE])) {
          $content_type = $para_item->field_content_type[LANGUAGE_NONE][0]['value'];
        }

        // Related topic filter.
        if (isset($para_item->field_related_health_topics[LANGUAGE_NONE])) {
          $related_topics = $para_item->field_related_health_topics[LANGUAGE_NONE][0]['target_id'];
        }

        // Audience filter.
        if (isset($para_item->field_audience[LANGUAGE_NONE])) {
          $audience = $para_item->field_audience[LANGUAGE_NONE][0]['tid'];
        }

        // Related initiative filter.
        if (isset($para_item->field_related_initiatives[LANGUAGE_NONE])) {
          $related_initiative = $para_item->field_related_initiatives[LANGUAGE_NONE][0]['target_id'];
        }

        $view->set_arguments([
          $content_type,
          $related_topics,
          $audience,
          $related_initiative,
        ]);
      }

      $view->execute();

      if ($view->total_rows > 0) {
        // Add grid if there is no manual grid col set.
        if ($view->style_plugin->options['row_class'] == '' && $para_item->bundle == 'para_view') {
          $view->style_plugin->options['row_class'] = implode(' ', _health_grid_class(count($view->result), []));
        }
        // Render the view.
        $variables['para_rendered_view'] = $view->preview();

        // If there are no more records to show, hide the more link.
        if ($view->total_rows <= count($view->result)) {
          $variables['content']['field_link_to']['#access'] = FALSE;
        }
        // Add some classes to help identify it.
        $variables['classes_array'][] = 'paragraphs-view-' . $view->name;
        $variables['classes_array'][] = 'paragraphs-view-display-' . $view->current_display;
      }
    }

    // 2 col para.
    if ($para_item->bundle == 'two_columns') {
      if (isset($para_item->field_pbundle_title[LANGUAGE_NONE])) {
        $title = $para_item->field_pbundle_title[LANGUAGE_NONE][0]['value'];

        // Add title to class.
        $variables['classes_array'][] = 'paragraphs-2-columns-' . strtolower(str_replace(' ', '-', $title));
      }
    }

    // Term para.
    if ($para_item->bundle == 'para_taxonomy') {
      $link_url = url($para_item->field_link_external[LANGUAGE_NONE][0]['url']);

      // Pass link to template.
      $variables['link_url'] = $link_url;
    }

    // Block para.
    if ($para_item->bundle == 'para_block') {
      // Add total block number in current para to class.
      $block_num = count($para_item->field_para_block_id[LANGUAGE_NONE]);
      $variables['classes_array'][] = 'block__count--' . $block_num;
      foreach ($variables['paragraphs_item']->field_para_block_id[LANGUAGE_NONE] as $key => $block) {
        $block['value'] .= '-num' . $block_num;
        $variables['paragraphs_item']->field_para_block_id[LANGUAGE_NONE][$key] = $block;
      }

      // Add class to identify specific block.
      foreach ($para_item->field_para_block_id[LANGUAGE_NONE] as $bid) {
        $variables['classes_array'][] = 'block-id__' . $bid['value'];
      }
    }

    // Recommendations.
    if ($para_item->bundle == 'recommendation') {
      // Add a class to identify what grade it is.
      $term = taxonomy_term_load($para_item->field_recommendation_grade[LANGUAGE_NONE][0]['tid']);
      $variables['classes_array'][] = 'au-recommendation--grade-' . _health_prepare_filename($term->name);
    }

    // Summary lists.
    if ($para_item->bundle == 'summary_list') {
      $output = '';
      $node = menu_get_object('node');
      switch ($para_item->field_summary_list_type[LANGUAGE_NONE][0]['value']) {
        case 'Recommendations':
          $output = _health_book_find_recommendations($node->book['bid']);
          break;

        case 'Tables':
          $tables = _health_book_tree_traverse($node->book['bid'], '_health_book_find_tables');
          $items = [];
          foreach ($tables as $nid => $group) {
            foreach ($group['tables'] as $table) {
              $items[] = l($table->field_title[LANGUAGE_NONE][0]['value'], 'node/' . $group['node']->nid, ['fragment' => _health_prepare_filename($table->field_title[LANGUAGE_NONE][0]['value'])]);
            }
          }
          $items = theme_item_list([
            'items' => $items,
            'title' => NULL,
            'type' => 'ul',
            'attributes' => [],
          ]);
          $output .= $items;
          break;

        case 'TOC':
          $output = theme_item_list([
            'items' => _health_book_table_of_contents($node->book['bid']),
            'title' => NULL,
            'type' => 'ul',
            'attributes' => ['class' => 'toc'],
          ]);
          break;
      }
      $variables['content']['field_summary_list_type'][0]['#markup'] = $output;
    }

    // Tables.
    if ($para_item->bundle == 'content_table') {
      // Move the title and summary into the table caption.
      $title = $variables['content']['field_title'][0]['#markup'];
      $table = $variables['content']['field_body'][0]['#markup'];
      $replacement = '$1<caption><p class="au-display-sm">' . $title . '</p>';
      if (array_key_exists('field_summary', $variables['content'])) {
        $replacement .= '<span class="au-sronly">' . $variables['content']['field_summary'][0]['#markup'] . '</span>';
      }
      $replacement .= '</caption>$2';
      $table = preg_replace('/(<table.*?>)(.*)/s', $replacement, $table);

      // Add the footer into tfoot.
      if (array_key_exists('field_table_source', $variables['content'])) {
        $footer = $variables['content']['field_table_source'][0]['#markup'];
        $table = preg_replace('/(.*)<\/table>/s', '$1<tfoot><tr><td class="text--minor">' . $footer . '</td></tr></tfoot></table>', $table);
        drupal_add_js(path_to_theme() . '/js/dist/health.tables.min.js', array(
          'type' => 'file',
          'scope' => 'footer',
          'weight' => 100,
        ));
      }

      // Create an anchor so it can be deep linked to.
      $variables['content']['field_body']['#prefix'] = '<a name="' . _health_prepare_filename($title) . '"></a>';

      // Set the updated table into content.
      $variables['content']['field_body'][0]['#markup'] = $table;
    }

    // Call out.
    if ($para_item->bundle == 'content_callout') {
      $variables['classes_array'][] = 'au-callout';
    }
  }

  // Custom token replacement.
  if (isset($variables['content']['field_bean_body'])) {
    $variables['content']['field_bean_body'][0]['#markup'] = str_replace(THEME_PATH_TOKEN_GENERIC, '/' . path_to_theme(), $variables['content']['field_bean_body'][0]['#markup']);
  }

  // Beans.
  if ($variables['entity_type'] == 'bean') {

    $bean = $variables['bean'];

    // Add template suggestions for bean types.
    $variables['theme_hook_suggestions'][] = 'block__bean__' . $bean->type;

    // Add grid class accordingly.
    $count = isset($variables['elements']['num']) ? $variables['elements']['num'] : 1;
    $variables['classes_array'] = _health_grid_class($count, $variables['classes_array'], '');

    if ($bean->type == 'image_and_text') {
      // Compose block wrapper link url.
      if ($bean->field_link_internal) {
        $variables['wrapper_link_url'] = url($bean->field_link_internal[LANGUAGE_NONE][0]['url'], $bean->field_link_internal[LANGUAGE_NONE][0]);
      }

      // Add 'active' to classes array if it is the current page of the image
      // and text block link.
      if ($bean->view_mode == 'view_selector') {
        $current_url = '/' . current_path();
        if (isset($bean->field_link_internal[LANGUAGE_NONE][0])) {
          $block_link = $bean->field_link_internal[LANGUAGE_NONE][0]['url'];
          if ($current_url == drupal_get_normal_path($block_link)) {
            $variables['classes_array'][] = 'active';
          }
        }
      }
    }

    // Add delta to class array.
    $variables['classes_array'][] = $bean->delta;

    // For share this and page accessibility link block.
    $token_blocks = [
      'share-this',
      'page-accessibility-link',
    ];
    if (in_array($bean->delta, $token_blocks)) {
      global $base_url;
      $current_url = drupal_encode_path($base_url . '/' . drupal_get_path_alias(current_path()));
      $current_title = drupal_get_title();
      $variables['field_bean_body'][0]['value'] = str_replace('[current-page:title]', $current_title, $variables['field_bean_body'][0]['value']);
      $variables['field_bean_body'][0]['value'] = str_replace('[current-page:url]', $current_url, $variables['field_bean_body'][0]['value']);
      $variables['content']['field_bean_body'][0]['#markup'] = $variables['field_bean_body'][0]['value'];
    }

    // Add hook for preprocess field in bean.
    if (isset($bean->preprocess_fields)) {
      foreach ($bean->preprocess_fields as $field) {
        $preprocess = 'health_preprocess_ds_' . $field;
        if (function_exists($preprocess)) {
          $variables[$field] = $preprocess($variables);
        }
      }
    }
  }

  // Display suite preprocess hooks.
  if (isset($variables['paragraphs_item']->preprocess_fields)) {
    foreach ($variables['paragraphs_item']->preprocess_fields as $field) {
      $preprocess = 'health_preprocess_ds_' . $field;
      if (function_exists($preprocess)) {
        $variables[$field] = $preprocess($variables);
      }
    }
  }
}

/**
 * Implements hook_preprocess_field().
 */
function health_preprocess_field(&$variables) {
  // Add line breaks to plain text on long text fields.
  if ($variables['element']['#field_type'] == 'text_long') {
    $field_name = $variables['element']['#field_name'];
    $lang = LANGUAGE_NONE;
    if (isset($variables['element']['#object']->language)) {
      $lang = $variables['element']['#object']->language;
    }
    foreach ($variables['items'] as $key => &$item) {
      if (isset($variables['element']['#object']->{$field_name}[$lang]) && $variables['element']['#object']->{$field_name}[$lang][$key]['format'] == NULL) {
        $item['#markup'] = nl2br($item['#markup']);
      }
    }
  }

  // Videos.
  if ($variables['element']['#bundle'] == 'video' && $variables['element']['#field_name'] == 'field_image_featured') {
    $nid = $variables['element']['#object']->nid;
    $video_node = node_load($nid);
    $youtube_code = $video_node->field_video_youtubeid[LANGUAGE_NONE][0]['value'];
    $variables['youtube_code'] = $youtube_code;
    // Add JS to embed video.
    $variables['items'][0]['#attached']['js'][] = drupal_get_path('theme', 'health') . '/js/dist/health.video.min.js';
  }

  // Statistic value - split up the value into parts so they can be styled.
  if ($variables['element']['#field_name'] == 'field_statistic_value') {

    $matches = array();
    preg_match_all("/(\D?)([\d\.]*)(.*)/", $variables['items'][0]['#markup'], $matches);

    $theme_vars = array();
    if (!empty($matches[1][0])) {
      $theme_vars['prefix'] = trim($matches[1][0]);
    }
    if (!empty($matches[2][0])) {
      $theme_vars['value'] = trim($matches[2][0]);
    }
    if (!empty($matches[3][0])) {
      $theme_vars['suffix'] = trim($matches[3][0]);
    }
    $variables['items'][0]['#markup'] = theme('statistic_value', $theme_vars);
  }

  // Statistic trend icon - replace with an actual icon.
  if ($variables['element']['#field_name'] == 'field_statistic_trend_show_icon') {

    if (isset($variables['element']['#items'][0])) {
      switch ($variables['element']['#items'][0]['value']) {
        case 'no':
          $variables['items'][0]['#markup'] = '<span></span>';
          break;

        case 'up-positive';
          $variables['items'][0]['#markup'] = theme('statistic_trend_icon', ['direction' => 'up', 'indication' => 'positive']);
          break;

        case 'down-positive';
          $variables['items'][0]['#markup'] = theme('statistic_trend_icon', ['direction' => 'down', 'indication' => 'positive']);
          break;

        case 'up-negative';
          $variables['items'][0]['#markup'] = theme('statistic_trend_icon', ['direction' => 'up', 'indication' => 'negative']);
          break;

        case 'down-negative';
          $variables['items'][0]['#markup'] = theme('statistic_trend_icon', ['direction' => 'down', 'indication' => 'negative']);
          break;
      }
    }
  }

  // Create variable for street address field.
  if ($variables['element']['#field_name'] == 'field_contact_street_address') {
    $variables['location_map'] = '';
    // Only show the map in event node view.
    if (arg(0) == 'node' && is_numeric(arg(1))) {
      $node = node_load(arg(1));
      if ($node->type == 'event') {
        $node = $variables['element']['#object'];
        $google_api = theme_get_setting('ga_api');
        $lat = isset($node->field_address_latitude[LANGUAGE_NONE]) ? $node->field_address_latitude[LANGUAGE_NONE][0]['value'] : '0';
        $long = isset($node->field_address_longitude[LANGUAGE_NONE]) ? $node->field_address_longitude[LANGUAGE_NONE][0]['value'] : 0;
        $src = GOOGLE_MAP_API . '?center=' . $lat . ',' . $long . '&zoom=13&size=300x300&maptype=roadmap&key=' . $google_api;
        $src .= '&markers=color:blue%7Clabel:S%7C' . $lat . ',' . $long;
        $address = $variables['items'][0]['#address'];
        $variables['location_map'] = '<img src="' . $src . '" alt="' . $address['thoroughfare'] . ' ' . $address['locality'] . '" />';
      }
    }
  }

  // Add title and organisation to postal address field.
  if ($variables['element']['#field_name'] == 'field_contact_postal_address') {
    $variables['title'] = $variables['element']['#object']->title;
    $variables['organisation'] = '';

    if (isset($variables['element']['#object']->field_contact_organisation[LANGUAGE_NONE])) {
      $variables['organisation'] = $variables['element']['#object']->field_contact_organisation[LANGUAGE_NONE][0]['value'];
    }
  }

  // Token replacement (node) for all labels.
  $variables['label'] = token_replace($variables['label'], array('node' => $variables['element']['#object']));

  // Feature image.
  if ($variables['element']['#field_name'] == 'field_image_featured') {
    // News.
    if (property_exists($variables['element']['#object'], 'type') && $variables['element']['#object']->type == 'news_article') {
      // If it is full display and the image is the default, don't show it.
      if ($variables['element']['#view_mode'] == 'full') {
        if ($variables['element'][0]['#item']['fid'] == 956) {
          $variables['items'][0]['#access'] = FALSE;
        }
      }
    }

    // Contact.
    if (property_exists($variables['element']['#object'], 'type') && $variables['element']['#object']->type == 'contact') {

      // Add classes and swap out defaults.
      switch ($variables['element']['#object']->field_contact_type[LANGUAGE_NONE][0]['tid']) {
        // Committees and groups.
        case 2211:
          if ($variables['element']['#view_mode'] != 'full') {
            $variables['items'][0]['#item'] = get_object_vars(file_load(6206));
            $variables['classes_array'][] = 'image--circle';
          } else {
            $variables['classes_array'] = [];
            $variables['items'][0]['#access'] = FALSE;
          }

          break;
        // Department of Health.
        case 2206:
        // Department of Health State or Territory Office.
        case 2246:
        // Service.
        case 2261:
          if ($variables['element']['#view_mode'] != 'full') {
            $variables['items'][0]['#item'] = get_object_vars(file_load(6166));
            $variables['classes_array'][] = 'image--circle';
          } else {
            $variables['classes_array'] = [];
            $variables['items'][0]['#access'] = FALSE;
          }
          break;
        // Minister.
        case 311:
          if ($variables['element']['#view_mode'] != 'full') {
            $variables['classes_array'][] = 'image--circle';
          }
          break;
        // Organisation.
        case 2251:
        // Department of Health Portfolio Agency.
        case 316:
          if ($variables['element']['#view_mode'] != 'full') {
            $variables['items'][0]['#item'] = get_object_vars(file_load(9346));
          } else {
            $variables['classes_array'] = [];
            $variables['items'][0]['#access'] = FALSE;
          }
          break;
        // Local health state or territory office
        case 2446:
          if (array_key_exists('is_default', $variables['items'][0]['#item']) && $variables['items'][0]['#item']['is_default'] == TRUE) {
            if ($variables['element']['#view_mode'] != 'full') {
              $variables['classes_array'][] = 'image--circle';
            } else {
              $variables['classes_array'] = [];
              $variables['items'][0]['#access'] = FALSE;
            }
          }
          break;
      }

    }
  }

  // Related term field.
  if ($variables['element']['#field_name'] == 'field_related_term') {
    $item_id = $variables['element']['#object']->item_id;
    // Add item ID to class.
    $variables['classes_array'][] = 'item-' . $item_id;
  }

  // Add p tags around plain text fields.
  $fields = [
    'field_summary',
  ];
  foreach ($fields as $field) {
    if ($variables['element']['#field_name'] == $field) {
      if (isset($variables['element']['#items'][0]) && array_key_exists('format', $variables['element']['#items'][0])) {
        // Plain text.
        if ($variables['element']['#items'][0]['format'] == NULL) {
          $variables['items'][0]['#markup'] = '<p>' . $variables['items'][0]['#markup'] . '</p>';
        }
      }
    }
  }

  // Markup changes to content.
  // We would use text format filters but we don't have access in GovCMS.
  // We also cannot do this via JS because Readspeaker uses the original HTML
  // and will ignore changes made via JS.
  $fields = [
    'field_body',
  ];
  foreach ($fields as $field) {
    if ($variables['element']['#field_name'] == $field) {
      if (isset($variables['element']['#items'][0])) {
        $markup = $variables['items'][0]['#markup'];

        // Add rs_preserve class to any H2's.
        $markup = preg_replace("/<h2>(.+?)<\/h2>/", '<h2 class="rs_preserve">$1</h2>', $markup);
        // Add a div around tables so they can be made responsive.
        $markup = str_replace('<table', '<div class="health-table__responsive"><table', $markup);
        $markup = str_replace('</table>', '</table></div>', $markup);

        // Footnotes.
        $markup = _health_book_footnote_links($markup);
        // References.
        $markup = _health_book_reference_links($markup);

        // Set glossary terms.
        $glossary = &drupal_static('book_glossary_terms');
        if (!empty($glossary)) {
          foreach ($glossary as $glossary_item) {
            $term_regex = str_replace('/', '\/', preg_quote(check_plain($glossary_item['term'])));
            $markup = preg_replace("/([\s\(\)<>\[\]%])(" . $term_regex . ")([\s\(\)<>\.\[\]%])/m", '$1<span class="definition--candidate">$2</span>$3', $markup);
          }
        }

        $variables['items'][0]['#markup'] = $markup;
      }
    }
  }

  // Table/figure footnote and references.
  if ($variables['element']['#field_name'] == 'field_table_source') {
    $variables['items'][0]['#markup'] = _health_book_footnote_links($variables['items'][0]['#markup']);
    $variables['items'][0]['#markup'] = _health_book_reference_links($variables['items'][0]['#markup']);
  }

  // Publication date.
  if ($variables['element']['#field_name'] == 'field_publication_date') {
    // Change the display of the date depending on the selected
    // format for this node.
    if ($field = field_get_items('node', $variables['element']['#object'], 'field_publication_date_display')) {
      $field_instance = field_info_instance('node', $variables['element']['#field_name'], $variables['element']['#bundle']);
      $display_settings = $field_instance['display'][$variables['element']['#view_mode']];
      switch ($field[0]['value']) {
        case 'month_year':
          $display_settings['settings']['format_type'] = 'govcms_month_year';
          break;

        case 'year':
          $display_settings['settings']['format_type'] = 'search_api_facetapi_YEAR';
          break;
      }
      $rendered = field_view_field('node', $variables['element']['#object'], $variables['element']['#field_name'], $display_settings);
      $variables['items'][0]['#markup'] = $rendered[0]['#markup'];
    }
  }

  // Render publication orderable nmm contact details.
  if ($variables['element']['#field_name'] == 'field_publication_nmm_id') {
    $render_array = [
      '#theme' => 'publication_nmm_contact',
    ];
    $variables['publication_nmm_contact'] = drupal_render($render_array);
  }

  // Email address.
  if ($variables['element']['#field_name'] == 'field_contact_email') {
    // If we are on a media page, add in the news id and title.
    $node = menu_get_object();
    if ($node && $node->type == 'departmental_media') {
      $subject = 'Media enquiry - News item ID' . $node->nid;
      $body = 'URL - ' . str_replace('+', '%20', urlencode($GLOBALS['base_url'] . base_path() . drupal_get_path_alias()));
      $variables['items'][0]['#markup'] = l($variables['items'][0]['#markup'], 'mailto:' . $variables['items'][0]['#markup'] . '?subject=' . $subject . '&body=' . $body);
    }

    // Apply spamspan.
    if ($variables['items']) {
      $variables['items'][0]['#markup'] = _spamspan_filter_process($variables['items'][0]['#markup'], NULL);
    }
  }

  // Footnotes.
  $footnotes = ['field_book_footnotes'];
  if (in_array($variables['element']['#field_name'], $footnotes)) {
    foreach ($variables['items'] as &$item) {
      $markup = $item['#markup'];

      // Split up the footnotes by p tags.
      if (preg_match_all("/<p\b[^>]*>(.+?)<\/p>/", $markup, $matches) > 0) {
        $footnotes_list = [];
        foreach ($matches[1] as $delta => $match) {
          // Pull out the reference token and apply markup replacement.
          if (preg_match_all("/[\s(&nbsp;)]*\[\[(\d+?)\]\][\s(&nbsp;)]*/", $match, $matches2) > 0) {
            foreach ($matches2[1] as $delta2 => $match2) {
              $footnotes_list[] = [
                'data' => theme('health_footnote', [
                  'id' => $match2,
                  'text' => str_replace($matches2[0][$delta2], '', $match),
                ]),
                'class' => ['text--minor', 'au-footnotes__footnote'],
              ];
            }
          }
          // No reference token found, just use the whole string.
          else {
            $footnotes_list[] = ['data' => $match, 'class' => ['text--minor']];
          }
        }
        $variables['items'][0]['#markup'] = theme('item_list', [
          'items' => $footnotes_list,
          'type' => 'ul',
          'attributes' => ['class' => ['au-footnotes', 'list--remove']],
        ]);
      }
    }
    $variables['items'][0]['#attached']['js'][] = drupal_get_path('theme', 'health') . '/js/dist/health.footnotes.min.js';
  }

  // References.
  if ($variables['element']['#field_name'] == 'field_book_references') {
    $markup = _health_replace_nbsp($variables['items'][0]['#markup']);
    // Split up the references by p tags.
    if (preg_match_all("/<p\b[^>]*>(.+?)<\/p>/", $markup, $matches) > 0) {
      $references = [];
      foreach ($matches[1] as $delta => $match) {
        // Pull out the reference token and apply markup replacement.
        if (preg_match_all("/\s*\{\{(.+?)\}\}\s*/", $match, $matches2) > 0) {
          foreach ($matches2[1] as $delta2 => $match2) {
            $references[] = [
              'data' => theme('health_reference', [
                'id' => $match2,
                'text' => str_replace($matches2[0][$delta2], '', $match),
              ]),
              'class' => ['text--minor'],
            ];
          }
        }
        // No reference token found, just use the whole string.
        else {
          $references[] = ['data' => $match, 'class' => ['text--minor']];
        }
      }
      $variables['items'][0]['#markup'] = theme('item_list', [
        'items' => $references,
        'type' => 'ul',
        'attributes' => ['class' => 'list--remove'],
      ]);
    }
    $variables['items'][0]['#attached']['js'][] = drupal_get_path('theme', 'health') . '/js/dist/health.footnotes.min.js';
  }

  // Related related conditions or diseases.
  if ($variables['element']['#field_name'] == 'field_related_conditions_disease') {
    // Add dynamic col class to item div according to the total number of items.
    $classes = [];
    $classes = _health_grid_class(count($variables['items']), $classes);
    $variables['ds-config']['fi-cl'] = implode(' ', $classes);
  }
}

/**
 * Override or insert variables into the html template.
 *
 * @param array $variables
 *   An array of variables to pass to the theme template.
 * @param string $hook
 *   The name of the template being rendered. This is usually "html", but can
 *   also be "maintenance_page" since health_preprocess_maintenance_page() calls
 *   this function to have consistent variables.
 */
function health_preprocess_html(array &$variables, $hook) {
  // Add google tag manager.
  $render_gtm = [
    '#theme' => 'google_tag_manager',
  ];
  $render_gtm['#attached']['js'][] = [
    'data' => drupal_get_path('theme', 'health') . '/js/dist/health.gtm.min.js',
    'type' => 'file',
  ];
  $variables['google_tag_manager'] = drupal_render($render_gtm);

  // Add variables and paths needed for HTML5 and responsive support.
  $variables['base_path'] = base_path();
  $variables['path_to_health'] = drupal_get_path('theme', 'health');
  // Get settings for HTML5 and responsive support. array_filter() removes
  // items from the array that have been disabled.
  $meta = array_filter((array) theme_get_setting('health_meta'));

  // Add html5 shim for lt IE 9.
  if (in_array('html5', $meta)) {
    $variables['page']['content']['#attached']['js'][] = [
      'data' => 'https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.min.js',
      'type' => 'external',
    ];
  }

  // Add no-js logic.
  $variables['page']['content']['#attached']['js'][] = [
    'data' => drupal_get_path('theme', 'health') . '/js/dist/health.nojs.min.js',
    'type' => 'file',
  ];

  $variables['default_mobile_metatags'] = in_array('meta', $meta);

  // Attributes for html element.
  $variables['html_attributes_array'] = array(
    'lang' => $variables['language']->language,
    'dir' => $variables['language']->dir,
  );

  // Send X-UA-Compatible HTTP header to force IE to use the most recent
  // rendering engine.
  // This also prevents the IE compatibility mode button to appear when using
  // conditional classes on the html tag.
  if (is_null(drupal_get_http_header('X-UA-Compatible'))) {
    drupal_add_http_header('X-UA-Compatible', 'IE=edge');
  }

  // Return early, so the maintenance page does not call any of the code below.
  if ($hook != 'html') {
    return;
  }

  // Serialize RDF Namespaces into an RDFa 1.1 prefix attribute.
  if ($variables['rdf_namespaces']) {
    $prefixes = array();
    foreach (explode("\n  ", ltrim($variables['rdf_namespaces'])) as $namespace) {
      // Remove xlmns: and ending quote and fix prefix formatting.
      $prefixes[] = str_replace('="', ': ', substr($namespace, 6, -1));
    }
    $variables['rdf_namespaces'] = ' prefix="' . implode(' ', $prefixes) . '"';
  }

  // Classes for body element. Allows advanced theming based on context
  // (home page, node of certain type, etc.)
  if (!$variables['is_front']) {
    // Add unique class for each page.
    $path = drupal_get_path_alias($_GET['q']);
    // Add unique class for each website section.
    list($section,) = explode('/', $path, 2);
    $arg = explode('/', $_GET['q']);
    if ($arg[0] == 'node' && isset($arg[1])) {
      if ($arg[1] == 'add') {
        $section = 'node-add';
      }
      elseif (isset($arg[2]) && is_numeric($arg[1]) && ($arg[2] == 'edit' || $arg[2] == 'delete')) {
        $section = 'node-' . $arg[2];
      }
    }
    $variables['classes_array'][] = drupal_html_class('section-' . $section);

    // Store the menu item since it has some useful information.
    $variables['menu_item'] = menu_get_item();
    if ($variables['menu_item']) {
      switch ($variables['menu_item']['page_callback']) {
        case 'views_page':
          // Is this a Views page?
          $variables['classes_array'][] = 'page-views';
          break;
      }
    }
  }
}

/**
 * Implements THEME_preprocess_node().
 *
 * Override or insert variables into the node templates.
 *
 * @param array $variables
 *   Variables to pass to the theme template.
 */
function health_preprocess_node(array &$variables) {

  $node = $variables['node'];

  // Add title and alt to default image for image field.
  if (isset($node->field_image_featured) && isset($node->field_image_featured[LANGUAGE_NONE][0]['is_default']) && $node->field_image_featured[LANGUAGE_NONE][0]['is_default'] == TRUE) {
    $variables['content']['field_image_featured'][0]['#item']['alt'] = t('Default image');
    $variables['content']['field_image_featured'][0]['#item']['title'] = t('Default image');
  }

  // Add $unpublished variable.
  $variables['unpublished'] = (!$variables['status']) ? TRUE : FALSE;

  // Set preview variable to FALSE if it doesn't exist.
  $variables['preview'] = isset($variables['preview']) ? $variables['preview'] : FALSE;

  // Add pubdate to submitted variable.
  $variables['pubdate'] = '<time pubdate datetime="' . format_date($variables['node']->created, 'custom', 'c') . '">' . $variables['date'] . '</time>';
  if ($variables['display_submitted']) {
    $variables['submitted'] = t('Submitted by !username on !datetime', array('!username' => $variables['name'], '!datetime' => $variables['pubdate']));
  }

  // If the node is unpublished, add the "unpublished" watermark class.
  if ($variables['unpublished'] || $variables['preview']) {
    $variables['classes_array'][] = 'watermark__wrapper';
  }

  // Add a class for the view mode.
  if (!$variables['teaser']) {
    $variables['classes_array'][] = 'view-mode-' . $variables['view_mode'];
  }

  // Add a class to show node is authored by current user.
  if ($variables['uid'] && $variables['uid'] == $GLOBALS['user']->uid) {
    $variables['classes_array'][] = 'node-by-viewer';
  }

  // Add node view_mode as a theme hook suggestion.
  if ($variables['view_mode']) {
    $variables['theme_hook_suggestions'][] = 'node__' . $variables['view_mode'];
  }

  // Add the type as a name to the node e.g. "News article" or "Event".
  $variables['type_name'] = node_type_get_name($variables['node']);

  // Statistic.
  if ($node->type == 'statistic') {

    // Add grid class to statistics card.
    if (isset($variables['content']['num'])) {
      $variables['classes_array'] = _health_grid_class($variables['content']['num'], $variables['classes_array']);
    }

    // Add no-trend variant if trend info hasn't been added.
    if (empty($node->field_statistic_trend_text)) {
      $variables['classes_array'][] = 'stat--no-trend';
    }
  }

  // Hide summary if there is a featured image for horizontal listing display
  // with initiative, program, campaign content type.
  if (in_array($node->type, ['initiative', 'program', 'campaign'])) {
    if ($variables['view_mode'] == 'listing_horizontal') {
      if (isset($node->field_image_featured[LANGUAGE_NONE][0])) {
        unset($variables['content']['field_summary']);
      }
    }
  }

  // Display suite preprocess hooks.
  if (isset($variables['preprocess_fields'])) {
    foreach ($variables['preprocess_fields'] as $field) {
      $preprocess = 'health_preprocess_ds_' . $field;
      if (function_exists($preprocess)) {
        $variables['content'][$field] = array(
          '#markup' => $preprocess($variables),
          '#weight' => _health_get_ds_field_weight($variables, $field),
        );
      }
    }
  }

  // Check to see if this role has access to view this content type.
  global $user;
  // Deny access to anonymous users for Help pages.
  if ($node->type == 'help') {
    if (array_key_exists(1, $user->roles)) {
      drupal_access_denied();
    }
  }

  // Books.
  $book_types = ['book_page'];
  if (in_array($node->type, $book_types)) {

    // Add css that will number the headings on the page.
    if (isset($node->field_exclude_from_numbering[LANGUAGE_NONE]) && $node->field_exclude_from_numbering[LANGUAGE_NONE][0]['value'] == 0) {
      preg_match("/^([\d\.]+)/", $node->title, $matches);
      if ($matches[0]) {
        $active_num = $matches[0];
        $noClearFix = 'display: inline-block !important;';
        $numberStyle = '.region-content { counter-reset: h2counter; }';
        for ($i = 2; $i <= 6; $i++) {
          $numberStyle .= '.region-content h' . $i . ':not(.no-number) { counter-reset: h' . ($i + 1) . 'counter; }';
          $numberStyle .= '.region-content h' . $i . ':not(.no-number):before { counter-increment: h' . $i . 'counter 1; content: "' . $active_num . '" ';
          for ($j = 2; $j <= $i; $j++) {
            $numberStyle .= '"." counter(h' . $j . 'counter) ';
          }
          $numberStyle .= '"\\0000a0";' . $noClearFix . '}';
        }
        drupal_add_css($numberStyle, 'inline');
      }
    }

    // Load and save glossary terms.
    if (isset($node->book['bid'])) {
      $glossary = &drupal_static('book_glossary_terms');
      $glossary = _health_book_tree_traverse($node->book['bid'], '_health_book_find_glossary');
      // Add all the glossary terms to javascript so it can be used on the page.
      if (!empty($glossary)) {
        drupal_add_js([
          'health' => [
            'definitions' => [
              'terms' => $glossary,
              'templates' => ['abbr' => theme('health_abbr'), 'glossary' => theme('health_glossary')],
            ],
          ],
        ], 'setting');
        drupal_add_js(path_to_theme() . '/js/dist/health.definitions.min.js', 'file');
      }
    }


  }

  // Survey.
  if ($node->type == 'survey') {
    if ($node->field_status[LANGUAGE_NONE][0]['value'] == 'Upcoming' || $node->field_status[LANGUAGE_NONE][0]['value'] == 'Closed') {
      $variables['content']['field_link_external']['#access'] = FALSE;
    }
    else {
      $variables['content']['field_link_external'][0]['#element']['attributes']['class'] = 'button';
      $variables['content']['survey_link_closed_']['#access'] = FALSE;
    }
  }

  // Publications.
  if ($node->type == 'publication') {
    // Hide last updated field if earlier or the same as date published.
    if ($variables['view_mode'] == 'full') {
      $last_updated = $variables['content']['field_date_updated']['#items'][0]['value'];
      // We just want the date, so strip off the time.
      $parts = explode(' ', $last_updated);
      $last_updated = strtotime($parts[0]);
      $publication_date = strtotime($variables['content']['field_publication_date']['#items'][0]['value']);
      if ($last_updated <= $publication_date) {
        // Do not render date updated.
        unset($variables['content']['field_date_updated']);
      }
    }
  }
}

/**
 * Implements THEME_preprocess_page().
 */
function health_preprocess_page(&$variables) {
  // LazyLoad is added via html.tpl.php. This means we can exclude it from IE8.
  $variables['page']['content']['#attached']['js'][] = [
    'data' => path_to_theme() . '/js/dist/health.extlink.min.js',
    'type' => 'file',
  ];
  $variables['page']['content']['#attached']['js'][] = [
    'data' => path_to_theme() . '/js/dist/health.mobilenav.min.js',
    'type' => 'file',
  ];
  $variables['page']['content']['#attached']['js'][] = [
    'data' => path_to_theme() . '/pancake/js/pancake.js',
    'type' => 'file',
  ];
  $variables['page']['content']['#attached']['js'][] = [
    'data' => path_to_theme() . '/js/dist/script.min.js',
    'type' => 'file',
  ];
  $variables['page']['content']['#attached']['js'][] = [
    'data' => path_to_theme() . '/js/dist/health.facetapi.min.js',
    'type' => 'file',
  ];
  $variables['page']['content']['#attached']['js'][] = [
    'data' => path_to_theme() . '/js/dist/health.anchors.min.js',
    'type' => 'file',
  ];
  $variables['page']['content']['#attached']['js'][] = [
    'data' => path_to_theme() . '/js/dist/health.nojs.min.js',
    'type' => 'file',
  ];
  $variables['page']['content']['#attached']['js'][] = [
    'data' => path_to_theme() . '/js/dist/health.alerts.min.js',
    'type' => 'file',
  ];


  // Add backtotop JS.
  $render_backtotop = [
    '#theme' => 'backtotop',
  ];
  $render_backtotop['#attached']['js'][] = [
    'data' => path_to_theme() . '/js/dist/health.backtotop.min.js',
    'type' => 'file',
  ];
  $variables['backtotop'] = drupal_render($render_backtotop);

  // Add readspeaker JS to all pages except content types listed in 'exclude'.
  $variables['readspeaker'] = '';
  $render_readspeaker = [
    '#theme' => 'readspeaker',
  ];
  $render_readspeaker['#attached']['js'][] = [
    'data' => READSPEAKER_URL,
    'type' => 'external',
  ];
  $render_readspeaker['#attached']['js'][] = [
    'data' => 'window.rsConf = { ui: { 
      disableDetachment: true, 
      mobileVertPos: "bottom=95",
      tools: { enlarge: false } 
    }};',
    'type' => 'inline',
  ];
  $variables['readspeaker'] = drupal_render($render_readspeaker);


  // Add full width indicator.
  $variables['full_width'] = FALSE;
  if (isset($variables['node'])) {
    if (theme_get_setting('full_content_types')[$variables['node']->type] !== 0) {
      $variables['full_width'] = TRUE;
    }
    else {
      $variables['full_width'] = FALSE;
    }
  }

  // Add parameters to JS.
  global $base_url;
  $variables['page']['content']['#attached']['js'][] = [
    'data' => [
      'health' => [
        'theme_path' => drupal_get_path('theme', 'health'),
        'current_path' => url(
          current_path(),
          [
            'absolute' => TRUE,
            'query' => drupal_get_query_parameters(),
          ]
        ),
        'base_url' => $base_url,
      ],
    ],
    'type' => 'setting',
  ];

  // Add our css.
  $variables['page']['content']['#attached']['css'][] = [
    'data' => drupal_get_path('theme', 'health') . '/css/styles.css',
    'media' => 'all',
    'type' => 'file',
    'preprocess' => TRUE,
    'group' => CSS_THEME,
    'every_page' => TRUE,
  ];

  $variables['page']['content']['#attached']['css'][] = [
    'data' => drupal_get_path('theme', 'health') . '/css/ie8.css',
    'type' => 'file',
    'browsers' => array('IE' => 'IE 8', '!IE' => FALSE),
    'group' => CSS_THEME,
    'weight' => 999,
  ];

  // Node.
  if (isset($variables['node'])) {

    // Get the node.
    $node = $variables['node'];

    // If we are viewing the draft, load that version instead.
    if (arg(2) == 'draft') {
      $node = node_load($variables['node']->nid, $variables['node']->workbench_moderation['current']->vid);
    }

    // Save summary into template variables for use in page.tpl.
    $summary = field_get_items('node', $node, 'field_summary');
    if ($summary && isset($summary[0]['value'])) {
      $variables['summary'] = $summary[0]['value'];
    }

    // Add page number to title for book pages.
    if (property_exists($node, 'field_page_number') && array_key_exists(LANGUAGE_NONE, $node->field_page_number)) {
      $variables['title'] = $node->field_page_number[LANGUAGE_NONE][0]['value'] . ' ' . $node->title;
    }

  }

  // Views.
  if ($view = views_get_page_view()) {
    $variables['summary'] = $view->display_handler->get_option('display_description');
  }

  // Set section title and summary visibility.
  $variables['section_title'] = NULL;
  $active_trail = menu_get_active_trail();
  if (!empty($active_trail)) {

    // Topics.
    if ($active_trail[1]['href'] == 'health-topics') {
      if (isset($variables['node'])) {
        if (count($active_trail) > 3) {
          if ($variables['node']->type != 'health_topic_hp') {

            if (array_key_exists('link_path', $active_trail['3'])) {
              $health_topic_hp = node_load(str_replace('node/', '', $active_trail[3]['link_path']));
            }
            if (array_key_exists('link_path', $active_trail['2'])) {
              $health_topic = node_load(str_replace('node/', '', $active_trail[2]['link_path']));
            }

            // Check if we are under a health professional section of the topic.
            if (isset($health_topic_hp) && $health_topic_hp && $health_topic_hp->type == 'health_topic_hp') {
              $variables['section_title'] = theme('health_section_link', ['title' => $health_topic_hp->title, 'path' => 'node/' . $health_topic_hp->nid]);
              $variables['summary'] = NULL;
              // Check if we are under the general section of the topic.
            }
            elseif (isset($health_topic) && $health_topic) {
              $variables['section_title'] = theme('health_section_link', ['title' => $health_topic->title, 'path' => 'node/' . $health_topic->nid]);
              $variables['summary'] = NULL;
            }
          }
        }
      }
    }

    // Conditions and diseases.
    // Title doesn't appear in the active trail, so set one manually.
    if ($active_trail[1]['href'] == 'conditions-and-diseases') {
      if (count($active_trail) > 2) {
        $variables['section_title'] = theme('health_section_link', ['title' => 'Conditions and diseases', 'path' => 'conditions-and-diseases']);
      }
    }

    // Resources.
    if ($active_trail[1]['href'] == 'resources') {

      // Books.
      if (isset($active_trail[2]['module']) && $active_trail[2]['module'] == 'book') {
        if (count($active_trail) > 3) {
          $variables['section_title'] = theme('health_section_link', [
            'title' => $active_trail[2]['title'],
            'path' => $active_trail[2]['href'],
            'subtitle' => $active_trail[2]['title'],
          ]);
          $variables['summary'] = NULL;
        }
        else {
          $variables['section_title'] = theme('health_section_link', ['title' => 'Resources', 'path' => 'resources/']);
        }
      }
      // Other resources.
      elseif (count($active_trail) > 3) {
        $variables['section_title'] = theme('health_section_link', ['title' => 'Resources', 'path' => 'resources/']);
      }
      else {
        $variables['section_title'] = NULL;
        if (isset($variables['node'])) {
          $variables['summary'] = NULL;
        }
      }
    }

    // Services
    // Title doesn't appear in the active trail, so set one manually.
    if ($active_trail[1]['href'] == 'services') {
      if (count($active_trail) > 2) {
        $variables['section_title'] = theme('health_section_link', ['title' => 'Services', 'path' => 'services']);
      }
    }

    // Initiatives and programs.
    if ($active_trail[1]['href'] == 'initiatives-and-programs') {
      if (count($active_trail) > 3) {
        $variables['section_title'] = theme('health_section_link', [
          'title' => $active_trail[2]['link_title'],
          'path' => $active_trail[2]['link_path'],
        ]);
        $variables['summary'] = FALSE;
      }
    }

    // News and events
    // Set title to be the third level menu trail.
    if ($active_trail[1]['href'] == 'news-and-events') {
      if (count($active_trail) > 3) {
        $variables['section_title'] = theme('health_section_link', ['title' => 'News and events', 'path' => 'news-and-events']);
        $variables['summary'] = NULL;
      }
    }

    // Committees and groups.
    if ($active_trail[1]['href'] == 'committees-and-groups') {
      if (count($active_trail) > 2) {
        $variables['section_title'] = theme('health_section_link', ['title' => 'Committees and groups', 'path' => 'committees-and-groups']);
      }
    }

    // About us.
    if ($active_trail[1]['href'] == 'node/1' || $active_trail[1]['href'] == 'node/1946') {
      if (count($active_trail) > 2) {
        $variables['section_title'] = theme('health_section_link', [
          'title' => $active_trail[1]['title'],
          'path' => $active_trail[1]['href'],
        ]);
      }
      if (count($active_trail) > 3) {
        $variables['summary'] = NULL;
      }
    }
  }

  // Add search api page title logic.
  // This should be removed after using funnelback.
  if (arg(0) == 'search' && arg(1)) {
    $variables['title'] = 'Search - ' . arg(1);
    $variables['section_title'] = NULL;
  }

  // Add the Initiatives and programs logo so it can be rendered in the header.
  if (isset($variables['node'])) {
    if ($variables['node']->type == 'program') {
      $logo = field_view_field('node', $variables['node'], 'field_image_featured', [
        'label' => 'hidden',
        'settings' => array('image_style' => 'title_section_image'),
      ]);
      if (!empty($logo)) {
        $variables['header_image'] = render($logo);
        $variables['node']->field_image_featured = [];
      }
    }
  }

  // Books.
  if (isset($variables['node'])) {
    if ($variables['node']->type == 'book_page' && property_exists($variables['node'], 'book')) {
      $book = node_load($variables['node']->book['bid']);
      $variables['title_alt'] = l($book->title, 'node/' . $book->nid);
    }
  }
}

/**
 * Override or insert variables into the region templates.
 *
 * @param array $variables
 *   Variables to pass to the theme template.
 * @param string $hook
 *   The name of the template being rendered ("region" in this case.)
 */
function health_preprocess_region(array &$variables, $hook) {

  // Regions with no-wrapper.
  $nowrappers = [
    'page_top',
    'header',
    'sidebar_first',
    'sidebar_second',
    'title',
    'content',
    'footer_top',
    'footer_bottom',
    'page_bottom',
    'title_supp',
    'content_bottom',
  ];

  // Use the region--no-wrapper template for these regions.
  foreach ($nowrappers as $nowrap) {
    if ($variables['region'] == $nowrap) {
      array_unshift($variables['theme_hook_suggestions'], 'region__no_wrapper');
    }
  }
}

/**
 * Implements theme_views_view_table().
 */
function health_preprocess_views_view_table(array &$variables) {
  $variables['view']->header['area']->options['content'] = str_replace(THEME_PATH_TOKEN_GENERIC, '/' . path_to_theme(), $variables['view']->header['area']->options['content']);
}

/**
 * Override or insert variables into the views list preprocess.
 *
 * @param array $variables
 *   Variables to pass to the theme template.
 */
function health_preprocess_views_view_list(array &$variables) {
  // Add a custom class to the views row based on the URL.
  if ($variables['view']->name == 'categories') {
    foreach ($variables['view']->result as $key => $result) {
      $custom_class = explode(
        '/',
        $result->field_field_link_to[0]['raw']['url']
      );
      $variables['classes_array'][$key] .= ' health-icon--' . end($custom_class);
    }
  }
}

/**
 * Implement THEME_preprocess_views_view_row_rss().
 *
 * Add node ID to the feed.
 * Fix link format.
 */
function health_preprocess_views_view_row_rss(&$variables) {
  $result = $variables['view']->result;
  $id = $variables['id'];
  $nid = $result[$id - 1]->entity;
  $variables['nid'] = $nid;
  $variables['link'] = str_replace('%3Ca%20href%3D%22/', '', $variables['link']);
  $variables['link'] = str_replace('%22%3Eview%3C/a%3E', '', $variables['link']);
}

/**
 * Implements THEME_preprocess_views_view_rss().
 */
function health_preprocess_views_view_rss(&$variables) {
  // Disable caching for health alert RSS feed.
  if ($variables['view']->plugin_name == 'rss' && $variables['view']->name == 'health_alerts') {
    drupal_add_http_header('Cache-Control', 'no-cache, no-store');
    drupal_page_is_cacheable(FALSE);
  }
}

/**
 * Implements THEME_preprocess_book_navigation().
 */
function health_preprocess_book_navigation(&$variables) {
  if (!empty($variables['next_url'])) {
    $node_path = drupal_get_normal_path(substr($variables['next_url'], 1));
    $node = _health_load_node_from_node_path($node_path);
    if (property_exists($node, 'field_page_number') && $node->field_page_number) {
      $variables['next_title'] = $node->field_page_number[LANGUAGE_NONE][0]['value'] . ' ' . $variables['next_title'];
    }
  }

  if (!empty($variables['prev_url'])) {
    $node_path = drupal_get_normal_path(substr($variables['prev_url'], 1));
    $node = _health_load_node_from_node_path($node_path);
    if (property_exists($node, 'field_page_number') && $node->field_page_number) {
      $variables['prev_title'] = $node->field_page_number[LANGUAGE_NONE][0]['value'] . ' ' . $variables['prev_title'];
    }
  }
}
