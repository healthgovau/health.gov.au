<?php
// Include inc files.
include_once drupal_get_path('theme', 'health') . '/includes/helper.inc';

CONST READSPEAKER_URL = 'https://sf1-as.readspeaker.com/script/5802/ReadSpeaker.js?pids=embhl';
CONST GOOGLE_MAP_API = '//maps.googleapis.com/maps/api/staticmap';
CONST RESOURCES_TYPE = [
  'image',
  'publication',
  'video',
  'audio',
  'app_or_tool',
];
CONST THEME_PATH_TOKEN_GENERIC = '[theme-path]';
CONST PUBLICATION_TYPE_INFOGRAPHIC = 2176;

/**
 * Override or insert variables into the block templates.
 *
 * @param array $variables
 *   Variables to pass to the theme template.
 * @param string $hook
 *   The name of the template being rendered ("block" in this case.)
 */
function health_preprocess_block(&$variables, $hook) {

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
    case 'menu_block':
    case 'blog':
    case 'book':
    case 'comment':
    case 'forum':
    case 'shortcut':
    case 'statistics':
      $variables['attributes_array']['role'] = 'navigation';
      break;
    case 'search':
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

  // For bean blocks.
  if ($variables['block']->module == 'bean') {
    // Get the bean elements.
    $beans = $variables['elements']['bean'];
    // There is only 1 bean per block.
    $bean_keys = element_children($beans);
    $bean = $beans[reset($bean_keys)];
    // Add bean type classes to the block.
    $variables['classes_array'][] = drupal_html_class(
      'block-bean-'.$bean['#bundle']
    );
    // Add template suggestions for bean types.
    $variables['theme_hook_suggestions'][] = 'block__bean__'.$bean['#bundle'];
  }

  // Add a class to the block if it is a block menu with the title as a link.
  if ($variables['elements']['#block']->module == 'menu_block' && $variables['elements']['#config']['title_link']) {
    $variables['classes_array'][] = 'block-menu-block--title-link';
  }

  // Facet blocks.
  if (key_exists('#facet', $variables['elements'])) {

    // Collapse all by default.
    $variables['collapsed'] = TRUE;

    // Get the currently selected facets from the URL.
    $query_string = drupal_get_query_parameters();
    if (isset($query_string['f'])) {
      foreach ($query_string['f'] as $string) {
        $parts = explode(':', $string);
        // If there is a selected value in this filter, then we open the filter group.
        if ($variables['elements']['#facet']['field'] == $parts[0]) {
          $variables['collapsed'] = FALSE;
        }
      }
    }
  }
}

/**
 * Override or insert variables for the breadcrumb theme function.
 *
 * @param $variables
 *   An array of variables to pass to the theme function.
 * @param $hook
 *   The name of the theme hook being called ("breadcrumb" in this case).
 *
 * @see govcms_parkes_breadcrumb()
 */
function health_preprocess_breadcrumb(&$variables, $hook) {
  // Provide a navigational heading to give context for breadcrumb links to
  // screen-reader users.
  if (empty($variables['title'])) {
    $variables['title'] = t('You are here');
  }
}

/**
 * Implements THEME_preprocess_entity().
 */
function health_preprocess_entity(&$variables) {
  // Paragraphs.
  if ($variables['elements']['#entity_type'] == 'paragraphs_item') {

    $para_item = $variables['paragraphs_item'];

    // Add the number of child paragraph items in the listing as a css class.
    // This assumes there is only one child paragraph listing in a paragraph.
    foreach($variables['content'] as $field => $data) {
      if ($data['#field_type'] == 'paragraphs' || $data['#field_type'] == 'entityreference') {
        $variables['classes_array'][] = 'listing__count--' . count($data['#items']);
      }
    }

    // Add bundle title to class.
    if (isset($para_item->field_pbundle_title[LANGUAGE_NONE][0])) {
      $title = $para_item->field_pbundle_title[LANGUAGE_NONE][0]['value'];
      $variables['classes_array'][] = strtolower(str_replace(' ', '-', $title));
    }

    // Statistic.
    if ($para_item->bundle == 'para_statistic') {
      // Add no-trend variant if trend info hasn't been added.
      if (empty($variables['elements']['#entity']->field_statistic_trend) && empty($variables['elements']['#entity']->field_statistic_trend_timeframe)) {
        $variables['classes_array'][] = 'no-trend';
      }
    }

    // Views.
    if ($para_item->bundle == 'para_view') {

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
        switch($view_mode) {
          case "latest_news_events_public":
            if ($nid = _health_get_featured_item('featured_news_events')) {
              $view->set_arguments([NULL, $nid]);
            }
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
        }
      }
      if ($view_name == 'latest_rsources') {
        switch($view_mode) {
          case "resources_for_public_in_hp":
          case "latest_resources_public":
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
      $view->execute();

      if ($view->total_rows > 0) {
        // Render the view.
        $variables['para_rendered_view'] = $view->preview();
        // If there are no more records to show, hide the more link.
        if ($view->total_rows <= count($view->result)) {
          $variables['content']['field_link_to']['#access'] = FALSE;
        }
        // Add some classes to help identify it.
        $variables['classes_array'][] = 'paragraphs-view-' . $view->name;
        $variables['classes_array'][] = 'paragraphs-view-display-' . $view->current_display;
        // Add css count.
        $variables['classes_array'][] = 'listing__count--' . count($view->result);
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
      $link_url = $para_item->field_link_external[LANGUAGE_NONE][0]['url'];

      // Pass link to template.
      $variables['link_url'] = $link_url;
    }

    // Block para.
    if ($para_item->bundle == 'para_block') {
      // Add total block number in current para to class.
      $block_num = count($para_item->field_para_block_id[LANGUAGE_NONE]);
      $variables['classes_array'][] = 'block__count--' . $block_num;

      // Add class to identify specific block.
      foreach($para_item->field_para_block_id[LANGUAGE_NONE] as $bid) {
        $variables['classes_array'][] = 'block-id__' . $bid['value'];
      }
    }
  }

  // Custom token replacement.
  if (isset($variables['content']['field_bean_body'])) {
    $variables['content']['field_bean_body'][0]['#markup'] = str_replace(THEME_PATH_TOKEN_GENERIC, '/' . path_to_theme(), $variables['content']['field_bean_body'][0]['#markup']);
  }

  // Beans.
  if ($variables['entity_type'] == 'bean') {

    $bean = $variables['bean'];

    // Add 'active' to classes array if it is the current page of the image and text block link.
    if ($bean->type == 'image_and_text' && $bean->view_mode == 'view_selector') {
      $current_url = '/' . current_path();
      if (isset(drupal_get_query_parameters()['f'])) {
        $query_string = array_values(drupal_get_query_parameters()['f']);
      }
      else {
        $query_string = '';
      }
      if (isset($bean->field_link_internal[LANGUAGE_NONE][0])) {
        $block_link = $bean->field_link_internal[LANGUAGE_NONE][0]['url'];
        if (isset($bean->field_link_internal[LANGUAGE_NONE][0]['query'])) {
          $block_query_string = array_values($bean->field_link_internal[LANGUAGE_NONE][0]['query']);
        }
        else {
          $block_query_string = '';
        }
        if ($current_url == drupal_get_normal_path($block_link) && $query_string == $block_query_string) {
          $variables['classes_array'][] = 'active';
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
      Global $base_url;
      $current_url = drupal_encode_path($base_url . '/' . drupal_get_path_alias(current_path()));
      $current_title = drupal_get_title();
      $variables['field_bean_body'][0]['value'] = str_replace('[current-page:title]', $current_title, $variables['field_bean_body'][0]['value']);
      $variables['field_bean_body'][0]['value'] = str_replace('[current-page:url]', $current_url, $variables['field_bean_body'][0]['value']);
      $variables['content']['field_bean_body'][0]['#markup'] = $variables['field_bean_body'][0]['value'];
    }

    // Add hook for preprocess field in bean.
    if (isset($bean->preprocess_fields)) {
      foreach($bean->preprocess_fields as $field) {
        $preprocess = 'health_preprocess_ds_' . $field;
        if (function_exists($preprocess)) {
          $variables[$field] = $preprocess($variables);
        }
      }
    }
  }

  // Display suite preprocess hooks.
  if (isset($variables['paragraphs_item']->preprocess_fields)) {
    foreach($variables['paragraphs_item']->preprocess_fields as $field) {
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
    drupal_add_js(drupal_get_path('theme', 'health') . '/js/health.video.js');
  }

  // Statistic value - split up the value into parts so they can be styled.
  if ($variables['element']['#field_name'] == 'field_statistic_value') {

    $matches = array();
    preg_match_all("/(\D?)([\d\.]*)(.*)/", $variables['items'][0]['#markup'], $matches);

    $output = '';
    if (!empty($matches[1][0])) {
      $output .= '<span class="statistic-value-prefix">' . trim($matches[1][0]) . '</span>';
    }
    if (!empty($matches[2][0])) {
      $output .= '<span class="statistic-value">' . trim($matches[2][0]) . '</span>';
    }
    if (!empty($matches[3][0])) {
      $output .= '<span class="statistic-value-suffix">' . trim($matches[3][0]) . '</span>';
    }
    $variables['items'][0]['#markup'] = $output;
  }

  // Statistic trend icon - replace with an actual icon.
  if ($variables['element']['#field_name'] == 'field_statistic_trend_show_icon') {

    if (isset($variables['element']['#items'][0])) {
      switch ($variables['element']['#items'][0]['value']) {
        case 'no':
          $variables['items'][0]['#markup'] = '<span></span>';
          break;
        case 'up-positive';
          $variables['items'][0]['#markup'] = '<i class="fas fa-arrow-up statistic-icon positive" aria-hidden="true"></i>';
          break;
        case 'down-positive';
          $variables['items'][0]['#markup'] = '<i class="fas fa-arrow-down statistic-icon positive" aria-hidden="true"></i>';
          break;
        case 'up-negative';
          $variables['items'][0]['#markup'] = '<i class="fas fa-arrow-up statistic-icon negative" aria-hidden="true"></i>';
          break;
        case 'down-negative';
          $variables['items'][0]['#markup'] = '<i class="fas fa-arrow-down statistic-icon negative" aria-hidden="true"></i>';
          break;
      }
    }
  }

  // Create variable for address field.
  if ($variables['element']['#field_name'] == 'field_street_address') {
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
        $variables['location_map'] ='<img src="' . $src . '" alt="' . $address['thoroughfare'] . ' ' . $address['locality'] .'" />';
      }
    }
  }

  // Hide last updated field if it is not later than date published.
  if ($variables['element']['#field_name'] == 'field_date_updated' && $variables['element']['#view_mode'] == 'full') {
    $node_type = $variables['element']['#object']->type;
    if (in_array($node_type, RESOURCES_TYPE)) {
      // Apply logic only to resources content types.
      if (isset($variables['element']['#items'][0])) {
        $last_updated = strtotime($variables['element']['#items'][0]['value']);
        // publication content type.
        if ($variables['element']['#bundle'] == 'publication') {
          $node = $variables['element']['#object'];
          if (isset($node->field_publication_date[LANGUAGE_NONE][0])) {
            $publication_date = $node->field_publication_date[LANGUAGE_NONE][0]['value'];

            if ($last_updated <= strtotime($publication_date)) {
              // Do not render this field.
              unset($variables['items']);
              $variables['classes_array'][] = 'sr-only';
            }
          }
        }
      }
    }
  }

  // Token replacement (node) for all labels.
  $variables['label'] = token_replace($variables['label'], array('node' => $variables['element']['#object']));

  // Feature image.
  if ($variables['element']['#field_name'] == 'field_image_featured') {
    // News.
    if ($variables['element']['#object']->type == 'news_article') {
      // If it is full display and the image is the default, don't show it.
      if ($variables['element']['#view_mode'] == 'full') {
        if ($variables['element'][0]['#item']['fid'] == 956) {
          $variables['items'][0]['#access'] = FALSE;
        }
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
    'field_title'
  ];
  foreach($fields as $field) {
    if ($variables['element']['#field_name'] == $field) {
      if (isset($variables['element']['#items'][0]) && key_exists('format', $variables['element']['#items'][0])) {
        if ($variables['element']['#items'][0]['format'] == NULL) { // Plain text
          $variables['items'][0]['#markup'] = '<p>' . $variables['items'][0]['#markup'] . '</p>';
        }
      }
    }
  }

  // Publication date
  if ($variables['element']['#field_name'] == 'field_publication_date') {
    // Change the display of the date depending on the selected format for this node.
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

  // For resources with featured image and document download link, add anchor
  // link under the featured image field.
  if ($variables['element']['#bundle'] == 'publication') {
    // Add JS.
    $variables['items'][0]['#attached']['js'][] = drupal_get_path('theme', 'health') . '/js/health.imageAnchor.js';

    // Add link wrapper and link to the image field.
    if ($variables['element']['#field_name'] == 'field_image_featured' && $variables['element']['#view_mode'] == 'full') {
      $variables['items'][0]['#prefix'] = '<div class="link-wrapper"><a href="#" class="link-wrapper">';
      $variables['items'][0]['#suffix'] = '</a></div><div class="go-to-download-link"><a href="#" class="link-wrapper" role="button">Go to download</a></div>';
    }
  }
}

/**
 * Override or insert variables into the html template.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered. This is usually "html", but can
 *   also be "maintenance_page" since health_preprocess_maintenance_page() calls
 *   this function to have consistent variables.
 */
function health_preprocess_html(&$variables, $hook) {
  // Add google tag manager.
  //drupal_add_js(drupal_get_path('theme', 'health') . '/js/health.gtm.js');
  $variables['google_tag_manager'] = theme('google_tag_manager', []);

  // Add variables and paths needed for HTML5 and responsive support.
  $variables['base_path'] = base_path();
  $variables['path_to_health'] = drupal_get_path('theme', 'health');
  // Get settings for HTML5 and responsive support. array_filter() removes
  // items from the array that have been disabled.
  $meta = array_filter((array)theme_get_setting('health_meta'));
  $variables['add_html5_shim'] = in_array('html5', $meta);
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
    $variables['rdf_namespaces'] = ' prefix="'.implode(' ', $prefixes).'"';
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
 * Override or insert variables into the maintenance page template.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("maintenance_page" in this case.)
 */
function health_preprocess_maintenance_page(&$variables, $hook) {
  health_preprocess_html($variables, $hook);
  health_preprocess_page($variables);
}

/**
 * Implements THEME_preprocess_node().
 * Override or insert variables into the node templates.
 *
 * @param array $variables
 *   Variables to pass to the theme template.
 * @param string $hook
 *   The name of the template being rendered ("node" in this case.)
 */
function health_preprocess_node(&$variables) {

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
    $variables['theme_hook_suggestions'][] = 'node__'. $variables['view_mode'];
  }

  // Add the type as a name to the node e.g. "News article" or "Event"
  $variables['type_name'] = node_type_get_name($variables['node']);

  // Display suite preprocess hooks.
  if (isset($variables['preprocess_fields'])) {
    foreach($variables['preprocess_fields'] as $field) {
      $preprocess = 'health_preprocess_ds_' . $field;
      if (function_exists($preprocess)) {
        $variables['content'][$field] = array(
          '#markup' => $preprocess($variables),
          '#weight' => _health_get_ds_field_weight($variables, $field)
        );
      }
    }
  }

  // Check to see if this role has access to view this content type.
  global $user;
  if (key_exists('node', $variables)) {
    // Deny access to anonymous users for Help pages.
    if ($variables['node']->type == 'help') {
      if (key_exists(1, $user->roles)) {
        drupal_access_denied();
      }
    }
  }

}

/**
 * Implements THEME_preprocess_page().
 */
function health_preprocess_page(&$variables) {
  // Add vuejs and backtotop JS to all pages.
  drupal_add_js(path_to_theme() . '/js/vue.min.js');
  drupal_add_js(path_to_theme() . '/js/health.backtotop.js');
  $variables['backtotop'] = theme('backtotop', []);
  
  // Add readspeaker JS to all pages except listing, homepage, topic and hp topic.
  $variables['readspeaker'] = '';
  $exclude = [
    'health_topic',
    'health_topic_hp',
    'listing',
    'landing_page'
  ];
  if (isset($variables['node']) && !in_array($variables['node']->type, $exclude)) {
    drupal_add_js(READSPEAKER_URL, 'external');
    drupal_add_js(path_to_theme() . '/js/readspeaker/ReadSpeakerColorSkin.js');
    $variables['readspeaker'] = theme('readspeaker', []);
  }

  // Add parameters to JS.
  drupal_add_js(
    [
      'health' => [
        'theme_path' => drupal_get_path('theme', 'health'),
        'current_path' => url(
          current_path(),
          [
            'absolute' => TRUE,
            'query' => drupal_get_query_parameters()
          ]
        ), 
      ]
    ],
    [
      'type' => 'setting'
    ]
  );

  // Always add summary. If it is not needed, it will be removed.
  if (isset($variables['node'])) {
    $summary = field_get_items('node', $variables['node'], 'field_summary');
    if ($summary && isset($summary[0]['value'])) {
      $variables['summary'] = $summary[0]['value'];
    }
  } else {
    // Check if this is a views page.
    if ($view = views_get_page_view()) {
      $variables['summary'] = $view->display_handler->get_option('display_description');
    }
  }

  // Pages under a topic or grand-parents pages should use the parents' title
  // in the title section and no summary.
  $active_trail = menu_get_active_trail();
  if (!empty($active_trail)) {

    // Topics
    if ($active_trail[1]['href'] == 'topics') {
      if (isset($variables['node'])) {
        if (count($active_trail) > 3) {
          if ($variables['node']->type != 'health_topic_hp') {

            if (key_exists('link_path', $active_trail['3'])) {
              $health_topic_hp = node_load(str_replace('node/', '', $active_trail[3]['link_path']));
            }
            if (key_exists('link_path', $active_trail['2'])) {
              $health_topic = node_load(str_replace('node/', '', $active_trail[2]['link_path']));
            }

            // Check if we are under a health professional section of the topic.
            if (isset($health_topic_hp) && $health_topic_hp && $health_topic_hp->type == 'health_topic_hp') {
              $variables['section_title'] = theme('health_section_link', ['title' => $health_topic_hp->title, 'path' => 'node/' . $health_topic_hp->nid]);
              $variables['summary'] = NULL;
              // Check if we are under the general section of the topic.
            } else if (isset($health_topic) && $health_topic) {
              $variables['section_title'] = theme('health_section_link', ['title' => $health_topic->title, 'path' => 'node/' . $health_topic->nid]);
              $variables['summary'] = NULL;
            }
          }
        }
      }
    }

    // Conditions and diseases
    // Title doesn't appear in the active trail, so set one manually.
    if ($active_trail[1]['href'] == 'conditions-and-diseases') {
      if (count($active_trail) > 2) {
        $variables['section_title'] = theme('health_section_link', ['title' => 'Conditions and diseases', 'path' => 'conditions-and-diseases']);
      }
    }

    // Resources
    if ($active_trail[1]['href'] == 'resources') {
      if (isset($variables['node'])) {
        $variables['summary'] = NULL;
      }
      if (count($active_trail) > 3) {
        $variables['section_title'] = theme('health_section_link', ['title' => 'Resources', 'path' => 'resources/']);
      } else {
        $variables['section_title'] = NULL;
      }
    }

    // Services
    // Title doesn't appear in the active trail, so set one manually.
    if ($active_trail[1]['href'] == 'services') {
      if (count($active_trail) > 2) {
        $variables['section_title'] = theme('health_section_link', ['title' => 'Services', 'path' => 'services']);
      }
    }

    // Initiatives and programs
    // Title doesn't appear in the active trail, so set one manually.
    if ($active_trail[1]['href'] == 'initiatives-and-programs') {
      if (count($active_trail) > 2) {
        $variables['section_title'] = theme('health_section_link', ['title' => 'Initiatives and programs', 'path' => 'initiatives-and-programs']);
      }
      if (count($active_trail) > 3) {
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

    // Committees and groups
    if ($active_trail[1]['href'] == 'committees-and-groups') {
      if (count($active_trail) > 2) {
        $variables['section_title'] = theme('health_section_link', ['title' => 'Committees and groups', 'path' => 'committees-and-groups']);
      }
    }

    // About us
    if ($active_trail[1]['href'] == 'node/1' || $active_trail[1]['href'] == 'node/1946') {
      if (count($active_trail) > 2) {
        $variables['section_title'] = theme('health_section_link', [
          'title' => $active_trail[1]['title'],
          'path' => $active_trail[1]['href']
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
    if ($variables['node']->type == 'initiative_or_program') {
      $logo = field_view_field('node', $variables['node'], 'field_image_featured', array('label'=> 'hidden', 'settings' => array('image_style' => 'title_section_image')));
      $variables['initiative_or_program_logo'] = render($logo);
      $variables['node']->field_image_featured = array();
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
function health_preprocess_region(&$variables, $hook) {

  // Regions with no-wrapper
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
    'content_bottom'
  ];

  // Use the region--no-wrapper template for these regions
  foreach($nowrappers as $nowrap) {
    if ($variables['region'] == $nowrap) {
      array_unshift($variables['theme_hook_suggestions'], 'region__no_wrapper');
    }
  }
}

/**
 * Override or insert variables into the views list preprocess.
 *
 * @param array $variables
 *   Variables to pass to the theme template.
 */
function health_preprocess_views_view_list(&$variables) {
  // Add a custom class to the views row based on the URL.
  if ($variables['view']->name == 'categories') {
    foreach ($variables['view']->result as $key => $result) {
      $custom_class = explode(
        '/',
        $result->field_field_link_to[0]['raw']['url']
      );
      $variables['classes_array'][$key] .= ' health-icon--'.end($custom_class);
    }
  }
}
