<?php

/**
 * @file
 * Contains the theme's functions to manipulate Drupal's default markup.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728096
 */

// Include inc files.
include_once drupal_get_path('theme', 'health') . '/includes/helper.inc';
include_once drupal_get_path('theme', 'health') . '/includes/book_helper.inc';
include_once drupal_get_path('theme', 'health') . '/includes/preprocess_hooks.inc';
include_once drupal_get_path('theme', 'health') . '/includes/ds_preprocess_hooks.inc';
include_once drupal_get_path('theme', 'health') . '/includes/process_hooks.inc';

// Auto-rebuild the theme registry during theme development.
if (theme_get_setting('health_rebuild_registry') && !defined('MAINTENANCE_MODE')) {
  // Rebuild .info data.
  system_rebuild_theme_data();
  // Rebuild theme registry.
  drupal_theme_rebuild();
  // Turn on template debugging.
  $GLOBALS['conf']['theme_debug'] = TRUE;
}

/**
 * Implements hook_theme().
 */
function health_theme() {
  $theme['document_accessibility_link'] = [
    'variables' => [
      'current_page' => NULL,
    ],
    'template' => 'document_accessibility_link',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['toc'] = [
    'variables' => [
      'full_width' => NULL,
    ],
    'template' => 'toc',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['backtotop'] = [
    'variables' => [],
    'template' => 'back_to_top',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['readspeaker'] = [
    'variables' => [],
    'template' => 'readspeaker',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['selected_filters_wrapper'] = [
    'variables' => [
      'selected_filters' => NULL,
    ],
    'template' => 'selected_filters_wrapper',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['selected_filter'] = [
    'variables' => [
      'url' => NULL,
      'classes' => NULL,
      'text' => NULL,
    ],
    'template' => 'selected_filter',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['public_hp_switcher'] = [
    'variables' => [
      'text' => NULL,
      'title' => NULL,
      'url' => NULL,
    ],
    'template' => 'public_hp_switcher',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['media_enquiry'] = [
    'template' => 'media_enquiry',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['google_tag_manager'] = [
    'variables' => [],
    'template' => 'health_google_tag_manager',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['publication_collection'] = [
    'variables' => [
      'collection_list' => NULL,
    ],
    'template' => 'publication_collection',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['health_section_link'] = [
    'variables' => [
      'title' => NULL,
      'path' => NULL,
      'subtitle' => NULL,
    ],
    'template' => 'section_link',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['statistic_value'] = [
    'variables' => [
      'prefix' => NULL,
      'value' => NULL,
      'suffix' => NULL,
    ],
    'template' => 'statistic_value',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['statistic_trend_icon'] = [
    'variables' => [
      'direction' => NULL,
      'indication' => NULL,
    ],
    'template' => 'statistic_trend_icon',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['health_breadcrumb'] = [
    'variables' => [
      'title' => NULL,
      'breadcrumb' => array(),
    ],
    'template' => 'health_breadcrumb',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['publication_nmm_contact'] = [
    'variables' => [],
    'template' => 'publication_nmm_contact',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['health_alert_bar'] = [
    'variables' => [],
    'template' => 'health_alert_bar',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['health_summary_list_group'] = [
    'variables' => [
      'items' => [],
      'node' => NULL,
    ],
    'template' => 'health_summary_list_group',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['health_footnote'] = [
    'variables' => [
      'id' => '',
      'text' => '',
    ],
    'template' => 'health_footnote',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['health_footnote-link'] = [
    'variables' => [
      'items' => [],
    ],
    'template' => 'health_footnote-link',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['health_reference'] = [
    'variables' => [
      'id' => '',
      'text' => '',
    ],
    'template' => 'health_reference',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['health_reference-link'] = [
    'variables' => [
      'items' => [],
    ],
    'template' => 'health_reference-link',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['health_abbr'] = [
    'variables' => [
      'term' => '',
      'definition' => '',
      'url' => '',
    ],
    'template' => 'health_abbreviation',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['health_glossary'] = [
    'variables' => [
      'term' => '',
      'definition' => '',
      'url' => '',
    ],
    'template' => 'health_definition',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['health_file_download_link'] = [
    'variables' => [
      'title' => '',
      'mime' => '',
      'size' => '',
      'pages' => '',
      'icon' => '',
      'uri' => '',
    ],
    'template' => 'health_file_download_link',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['health_alert_close_button'] = [
    'variables' => [
      'nid' => NULL,
    ],
    'template' => 'health_alert_close_button',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['health_separator'] = [
    'template' => 'health_separator',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  return $theme;
}

/**
 * Implements THEME_breadcrumb().
 */
function health_breadcrumb($variables) {
  // Override breadcrumb if current page is a search result page using default
  // search API.
  $crumbs_trail = array_keys($variables['crumbs_trail']);
  if ($crumbs_trail[1] == 'search' && !empty($crumbs_trail[2])) {
    $variables['breadcrumb'][2] = 'Search - ' . arg(1);
  }
  return theme('health_breadcrumb', $variables);
}

/**
 * Implements hook_html_head_alter().
 */
function health_html_head_alter(&$head) {
  // Simplify the meta tag for character encoding.
  if (isset($head['system_meta_content_type']['#attributes']['content'])) {
    $head['system_meta_content_type']['#attributes'] = [
      'charset' => str_replace('text/html; charset=', '', $head['system_meta_content_type']['#attributes']['content']),
    ];
  }
}

/**
 * Implements hook_page_alter().
 *
 * Look for the last block in the region. This is impossible to determine from
 * within a preprocess_block function.
 */
function health_page_alter(&$page) {
  // Look in each visible region for blocks.
  foreach (system_region_list($GLOBALS['theme'], REGIONS_VISIBLE) as $region => $name) {
    if (!empty($page[$region])) {
      // Find the last block in the region.
      $blocks = array_reverse(element_children($page[$region]));
      while ($blocks && !isset($page[$region][$blocks[0]]['#block'])) {
        array_shift($blocks);
      }
      if ($blocks) {
        $page[$region][$blocks[0]]['#block']->last_in_region = TRUE;
      }
    }
  }
}

/**
 * Implements hook_form_BASE_FORM_ID_alter().
 *
 * Append selected filter links to 'contains' search box.
 */
function health_form_views_exposed_form_alter(&$form, &$form_state, $form_id) {
  if (isset($form['#info']['filter-search_api_views_fulltext'])) {
    // Add placeholder.
    $form['search_api_views_fulltext']['#attributes']['placeholder'] = t('Enter your search term');

    // Add classes.
    $form['search_api_views_fulltext']['#attributes']['class'][] = 'au-search__form__input au-text-input';
    $form['search_api_views_fulltext']['#prefix'] = '<div class="au-search__form__input-wrapper">';
    $form['search_api_views_fulltext']['#suffix'] = '</div>';
    $form['submit']['#attributes']['class'][] = 'au-search__form__submit au-btn';
    $form['#attributes']['class'] = 'au-search au-search--listing au-search__form col-md-9 col-xs-12';

    // Append selected filters.
    $query_string = drupal_get_query_parameters();

    $links = [];

    $form['#suffix'] = '';

    // Create a filter to remove the search term.
    if (isset($query_string['search_api_views_fulltext'])) {
      $query_string_modified = $query_string;
      unset($query_string_modified['search_api_views_fulltext']);

      $links[] = theme('selected_filter', [
        'url' => url('/' . current_path(), ['query' => $query_string_modified]),
        'text' => t('@text', ['@text' => $query_string['search_api_views_fulltext']]),
      ]);
    }

    // Add form label.
    $form['search_api_views_fulltext']['#title'] = t('Search list');

    if (isset($query_string['f']) && !empty($query_string['f'])) {
      // Find selected filters.
      foreach ($query_string['f'] as $key => $param) {
        $url = '';
        $query_string_modified = $query_string['f'];
        $filter_name = _health_find_facet_filter_name($param);
        // Re compose query string.
        unset($query_string_modified[$key]);
        if (isset($query_string['search_api_views_fulltext'])) {
          $url .= 'search_api_views_fulltext=' . $query_string['search_api_views_fulltext'];
        }
        foreach ($query_string_modified as $key_1 => $item) {
          $url .= '&f[' . $key_1 . ']=' . $item;
        }

        $links[] = theme('selected_filter', [
          'url' => '/' . current_path() . '?' . $url,
          'text' => $filter_name,
        ]);
      }
    }

    if ($links) {
      // Default link to clear the search and filters.
      $clear_all = l(t('Clear all'),
        '/' . current_path(),
        [
          'attributes' => [
            'class' => 'clear-all',
            'title' => 'Clear all',
          ],
        ]
      );

      $form['#suffix'] .= theme('selected_filters_wrapper', [
        'selected_filters' => theme_item_list([
          'items' => $links,
          'type' => 'ul',
          'attributes' => ['class' => 'au-tags'],
          'title' => '',
        ]),
        'clear_all' => $clear_all,
      ]);
    }
  }
}

/**
 * Implements hook_form_alter().
 */
function health_form_alter(&$form, &$form_state, $form_id) {

  // Alter user feedback webform.
  if ($form['#form_id'] == 'webform_client_form_21') {
    // Add maxlength.
    _health_add_maxlength($form, [
      'suggestions_for_improvement' => 1200,
      'feedback' => 1200,
    ]);

    // Add a unique id so it can be referenced in an email.
    $date = new DateTime();
    $form['submitted']['reference']['#default_value'] = 'REF-' . $date->format('yj') . '-' . _health_gen_uid(4);

    // Add referrer, where the use came from.
    $form['submitted']['referrer']['#default_value'] = $_SERVER['HTTP_REFERER'];

    // Make sure this page isn't cache by Akamai or Drupal.
    drupal_add_http_header('Cache-Control', 'no-cache, no-store');
    drupal_page_is_cacheable(FALSE);
  }

  // Workbench moderation form.
  if ($form_id == 'workbench_moderation_moderate_form') {
    array_unshift($form['#submit'], '_health_date_submitter');
  }

}

/**
 * Submit handler for date updated and date published.
 *
 * Uses health_adminimal functions.
 *
 * @param array $form
 *   The form.
 * @param array $form_state
 *   The form state.
 */
function _health_date_submitter(array $form, array &$form_state) {
  require drupal_get_path('theme', 'health_adminimal') . '/template.php';
  _health_adminimal_date_published_submitter($form, $form_state);
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function health_form_search_api_page_search_form_alter(&$form, &$form_state) {

  if (array_key_exists('keys_1', $form)) {
    $form['keys_1']['#attributes']['placeholder'] = t('Enter your search terms');
    $form['keys_1']['#attributes']['class'][] = 'au-search__form__input au-text-input';
    $form['keys_1']['#prefix'] = '<div class="au-search__form__input-wrapper">';
    $form['keys_1']['#suffix'] = '</div>';
    $form['keys_1']['#attributes']['size'] = 30;
    $form['submit_1']['#attributes']['class'][] = 'au-search__form__submit au-btn';
  }
  elseif (array_key_exists('form', $form)) {
    $form['form']['keys_1']['#attributes']['placeholder'] = t('Enter your search terms');
    $form['form']['keys_1']['#attributes']['class'][] = 'au-search__form__input au-text-input';
    $form['form']['keys_1']['#prefix'] = '<div class="au-search__form__input-wrapper">';
    $form['form']['keys_1']['#suffix'] = '</div>';
    $form['form']['submit_1']['#attributes']['class'][] = 'au-search__form__submit au-btn';
  }

  $form['#attributes']['class'] = 'au-search__form';
}

/**
 * Implements hook_js_alter().
 *
 * Perform necessary alterations to the JavaScript before it is presented on
 * the page.
 */
function health_js_alter(&$javascript) {
  // Replace jQuery version on non admin pages.
  $replace_jquery = TRUE;

  // Array of additional admin paths.
  $extra_admin_paths = array(
    'node/*/edit',
    'node/add/*',
    'media/browser*',
    'media/ajax*',
  );

  // Do not apply to admin pages that may require lower version of jQuery
  // provided by Drupal core.
  if (path_is_admin(current_path())) {
    $replace_jquery = FALSE;
  }
  else {
    foreach ($extra_admin_paths as $extra_admin_path) {
      if (drupal_match_path(current_path(), $extra_admin_path)) {
        $replace_jquery = FALSE;
      }
    }
  }

  if ($replace_jquery) {
    $javascript['misc/jquery.js']['data'] = 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js';
    $javascript['misc/jquery.js']['type'] = 'external';
    unset($javascript['misc/jquery.js']);
  }

  // Move all JS to the footer, to improve page load.
  foreach ($javascript as $key => $js) {
    $javascript[$key]['scope'] = 'footer';
  }

  // Remove unused js.
  unset($javascript['profiles/govcms/modules/contrib/field_group/field_group.js']);
  unset($javascript['profiles/govcms/modules/contrib/superfish/superfish.js']);
  unset($javascript['profiles/govcms/libraries/superfish/superfish.js']);
  unset($javascript['profiles/govcms/libraries/superfish/supersubs.js']);
  unset($javascript['profiles/govcms/libraries/superfish/supposition.js']);
  unset($javascript['profiles/govcms/modules/contrib/toc_filter/toc_filter.js']);
}

/**
 * Implements hook_css_alter().
 */
function health_css_alter(&$css) {
  // Remove unused css.
  unset($css['modules/book/book.css']);
  unset($css['profiles/govcms/modules/contrib/ctools/css/ctools.css']);
  unset($css['profiles/govcms/modules/contrib/date/date_api/date.css']);
  unset($css['profiles/govcms/modules/contrib/date/date_popup/themes/datepicker.1.7.css']);
  unset($css['modules/field/theme/field.css']);
  unset($css['profiles/govcms/modules/contrib/panels/css/panels.css']);
  unset($css['profiles/govcms/modules/contrib/picture/picture_wysiwyg.css']);
  unset($css['modules/search/search.css']);
  unset($css['modules/user/user.css']);
  unset($css['profiles/govcms/modules/contrib/video_filter/video_filter.css']);
  unset($css['profiles/govcms/modules/contrib/toc_filter/toc_filter.css']);
  unset($css['profiles/govcms/libraries/superfish/css/superfish.css']);
  unset($css['sites/default/themes/site/health/superfish.css']);
  unset($css['profiles/govcms/modules/contrib/views/css/views.css']);
  unset($css['profiles/govcms/modules/contrib/facetapi/facetapi.css']);

  // Make sure some css is not rendered on IE8.
  if (array_key_exists('profiles/govcms/modules/contrib/ds/layouts/ds_2col/ds_2col.css', $css)) {
    $css['profiles/govcms/modules/contrib/ds/layouts/ds_2col/ds_2col.css']['browsers'] = array('IE' => 'gt IE 8');
  }
  if (array_key_exists('profiles/govcms/modules/contrib/ds/layouts/ds_2col_stacked/ds_2col_stacked.css', $css)) {
    $css['profiles/govcms/modules/contrib/ds/layouts/ds_2col_stacked/ds_2col_stacked.css']['browsers'] = array('IE' => 'gt IE 8');
  }
}

/**
 * Implements hook_file_entity_download_link().
 *
 * Output differently for Image content type.
 */
function health_file_entity_download_link($variables) {

  $nid = arg(1);
  if (is_numeric($nid)) {
    $node = node_load($nid);

    // If this is not an image or publication content type, do the normal
    // formatting.
    $types = ['image', 'publication', 'foi_request'];
    if (!in_array($node->type, $types)) {
      return theme_file_entity_download_link($variables);
    }
    else {

      // Grab the file.
      $file = $variables['file'];

      // Get file title to use in analytics and accessibility.
      // Default is the resource title.
      $title = $node->title;

      // Publications.
      if ($node->type == 'publication' || $node->type == 'foi_request') {
        $docs = $node->field_publication_files[$node->language];
        foreach ($docs as $doc) {
          $entities = entity_load('paragraphs_item', [$doc['value']]);
          if (!empty($entities)) {
            $para_documents = array_pop($entities);
            foreach ($para_documents->field_resource_document[LANGUAGE_NONE] as $resource_document) {
              $entities = entity_load('paragraphs_item', [$resource_document['value']]);
              if (!empty($entities)) {
                $para_document = array_pop($entities);
                if ($para_document->field_file[LANGUAGE_NONE][0]['fid'] == $file->fid) {
                  // Multiple document parts.
                  if (count($docs) > 1) {
                    $title .= ': ' . $para_documents->field_resource_file_title[LANGUAGE_NONE][0]['value'];
                  }
                  // Get page count.
                  if (isset($para_document->field_resource_file_pages)) {
                    $no_of_pages = $para_document->field_resource_file_pages[LANGUAGE_NONE][0]['value'];
                  }
                }
              }
            }
          }
        }
      }

      // Add metatdata (file size, image size, no of pages).
      // Round to 1 decimal for MB and whole number for KB in terms of the
      // file size format.
      $file_size = explode(' ', format_size($file->filesize));
      if (isset($file_size[1])) {
        if ($file_size[1] == 'MB') {
          $formatted_filesize = round($file_size[0], 1) . ' ' . $file_size[1];
        }
        else {
          $formatted_filesize = round($file_size[0], 0) . ' ' . $file_size[1];
        }
      }

      if (isset($no_of_pages)) {
        if ($no_of_pages > 1) {
          $no_of_pages .= ' pages';
        }
        else {
          $no_of_pages .= ' page';
        }
      }

      // Get the icon.
      $icon_directory = $variables['icon_directory'];
      $icon = theme('file_icon', array('file' => $file, 'icon_directory' => $icon_directory));

      // Get the path to the file.
      $uri = file_entity_download_uri($file);

      // Set options as per anchor format described at
      // http://microformats.org/wiki/file-format-examples
      $uri['options']['attributes']['type'] = $file->filemime . '; length=' . $file->filesize;

      // Add filename attribute for analytics.
      $uri['options']['attributes']['data-filename'] = $title;
      $uri['options']['attributes']['data-filetype'] = $file->filemime;

      // Output the link.
      $output = theme('health_file_download_link', [
        'title' => $title,
        'mime' => health_get_friendly_mime($file->filemime),
        'size' => $formatted_filesize,
        'pages' => $no_of_pages,
        'icon' => $icon,
        'uri' => $uri,
      ]);

      return $output;
    }
  }

  return theme_file_entity_download_link($variables);

}

/**
 * Implements theme_file_icon().
 */
function health_file_icon($variables) {
  $file = $variables['file'];
  $alt = $variables['alt'];
  $icon_directory = $variables['icon_directory'];
  $mime = check_plain($file->filemime);
  $icon_url = file_icon_url($file, $icon_directory);
  return '<img class="au-file__icon" alt="' . check_plain($alt) . '" title="' . $mime . '" src="' . $icon_url . '" />';
}

/**
 * Implements theme_webform_element().
 */
function health_webform_element($variables) {
  // Ensure defaults.
  $variables['element'] += array(
    '#title_display' => 'before',
  );

  $element = $variables['element'];

  // All elements using this for display only are given the "display" type.
  if (isset($element['#format']) && $element['#format'] == 'html') {
    $type = 'display';
  }
  else {
    $types = ['markup', 'textfield', 'webform_email', 'webform_number'];
    if (isset($element['#type']) && !in_array($element['#type'], $types)) {
      $type = $element['#type'];
    }
    else {
      $type = $element['#webform_component']['type'];
    }
  }

  // Convert the parents array into a string, excluding the "submitted" wrapper.
  $nested_level = $element['#parents'][0] == 'submitted' ? 1 : 0;
  $parents = str_replace('_', '-', implode('--', array_slice($element['#parents'], $nested_level)));

  $wrapper_attributes = isset($element['#wrapper_attributes']) ? $element['#wrapper_attributes'] : array('class' => array());
  $wrapper_classes = array(
    'au-form__item',
    'webform-component',
    'webform-component-' . $type,
  );
  if (isset($element['#title_display']) && strcmp($element['#title_display'], 'inline') === 0) {
    $wrapper_classes[] = 'webform-container-inline';
  }
  $wrapper_attributes['class'] = array_merge($wrapper_classes, $wrapper_attributes['class']);
  $wrapper_attributes['id'] = 'webform-component-' . $parents;
  $output = '<div ' . drupal_attributes($wrapper_attributes) . '>' . "\n";

  // If #title_display is none, set it to invisible instead - none only used if
  // we have no title at all to use.
  if ($element['#title_display'] == 'none') {
    $variables['element']['#title_display'] = 'invisible';
    $element['#title_display'] = 'invisible';
    if (empty($element['#attributes']['title']) && !empty($element['#title'])) {
      $element['#attributes']['title'] = $element['#title'];
    }
  }
  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }
  $prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . _webform_filter_xss($element['#field_prefix']) . '</span> ' : '';
  $suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . _webform_filter_xss($element['#field_suffix']) . '</span>' : '';

  // Description text.
  // Always output description text between the label and field.
  $description = '';
  if (!empty($element['#description'])) {
    $description = ' <div class="au-form__item-description">' . $element['#description'] . "</div>\n";
  }

  switch ($element['#title_display']) {
    case 'inline':
    case 'before':
    case 'invisible':
      $output .= ' ' . theme('form_element_label', $variables);
      $output .= $description;
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;

    case 'after':
      $output .= ' ' . $prefix . $element['#children'] . $suffix;
      $output .= $description;
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output .= $description;
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  $output .= "</div>\n";

  return $output;
}


/**
 * Implements theme_menu_tree().
 */
function health_menu_tree($variables) {
  return '<ul class="au-link-list">' . $variables['tree'] . '</ul>';
}

/**
 * Implements theme_menu_tree().
 */
function health_menu_tree__menu_sub_menu($variables) {
  return '<ul class="au-link-list au-link-list--inline">' . $variables['tree'] . '</ul>';
}

/**
 * Implements theme_menu_link().
 *
 * Add default audience filter to specific paths.
 *
 * @todo Is loading all the menu nodes here a performance issue?
 */
function health_menu_link(array $variables) {
  if ($query = _health_default_audience_menu($variables['element']['#href'])) {
    $variables['element']['#localized_options']['query'] = $query;
  }
  // Mark unpublished pages, only if logged in.
  global $user;
  if (array_key_exists(2, $user->roles)) {
    if ($node = _health_load_node_from_node_path($variables['element']['#href'])) {
      if ($node->status == 0) {
        $variables['element']['#localized_options']['attributes']['class'][] = 'au-side-nav--unpublished';
      }
    }
  }

  // Add page numbers for books, if available.
  if ($node = _health_load_node_from_node_path($variables['element']['#href'])) {
    if (property_exists($node, 'field_page_number') && $node->field_page_number) {
      $variables['element']['#title'] = $node->field_page_number[LANGUAGE_NONE][0]['value'] . ' ' . $variables['element']['#title'];
    }
  }

  // Add active class to the active list item if it doesn't have it.
  // This is needed for book navigation side nav to work with
  // the @gov.au/side-nav module.
  if ($variables['element']['#href'] == $_GET['q'] && !in_array('active', $variables['element']['#attributes']['class'])) {
    $variables['element']['#attributes']['class'][] = 'active';
  }

  return theme_menu_link($variables);
}

/**
 * Implements theme_superfish_menu_item_link().
 *
 * Add default audience filter to specific paths.
 */
function health_superfish_menu_item_link($variables) {
  if ($query = _health_default_audience_menu($variables['menu_item']['link']['href'])) {
    $variables['link_options']['query'] = $query;
  }
  return theme_superfish_menu_item_link($variables);
}

/**
 * Implements theme_crumbs_breadcrumb_link().
 *
 * Add default audience filter to specific paths.
 */
function health_crumbs_breadcrumb_link(array $item) {

  if ($query = _health_default_audience_menu($item['href'])) {
    $item['localized_options']['query'] = $query;
  }
  return theme_crumbs_breadcrumb_link($item);
}

/**
 * Implements hook_block_view_alter().
 */
function health_block_view_alter(&$data, $block) {
  if ($block->title == 'Intended audience' && $block->module == 'facetapi' && isset($data)) {
    foreach ($data['content']['field_audience']['#items'] as $key => $item) {
      // Replace (0) with ''.
      $item['data'] = str_replace('(0)', '', $item['data']);
      $data['content']['field_audience']['#items'][$key] = $item;
    }
  }

  // Clean up the workbench moderation block.
  if ($block->module == 'workbench' && $block->delta == 'block') {
    $data['content']['#markup'] = str_replace('Revision state: ', '', $data['content']['#markup']);
    $data['content']['#markup'] = str_replace('Most recent revision: ', '', $data['content']['#markup']);
    $data['content']['#markup'] = str_replace('<em>Yes</em>', '', $data['content']['#markup']);
    $data['content']['#markup'] = str_replace('<em>No</em>', '', $data['content']['#markup']);
    $data['content']['#markup'] = str_replace('<br />', '', $data['content']['#markup']);
    $data['content']['#markup'] = str_replace('Actions: ', '<br/>', $data['content']['#markup']);
    $data['content']['#markup'] = str_replace('Set moderation state:', '<i class="fa fa-arrow-right"></i>', $data['content']['#markup']);
  }
}

/**
 * Implements theme_image().
 */
function health_image($variables) {
  // If this is an SVG, output the full SVG, not an IMG.
  if (pathinfo(drupal_realpath($variables['path']), PATHINFO_EXTENSION) == 'svg') {
    return file_get_contents(drupal_realpath($variables['path']));
  }

  // If not an SVG, load normally with lazy loading.
  $attributes = $variables['attributes'];
  $attributes['src'] = file_create_url($variables['path']);
  foreach (array('width', 'height', 'alt', 'title') as $key) {
    if (isset($variables[$key])) {
      $attributes[$key] = $variables[$key];
    }
  }

  // Output an empty alt tag if an alt value hasn't been specified.
  if (!array_key_exists('alt', $attributes)) {
    $attributes['alt'] = '';
  }

  // Set data-src instead of src for lazy loading.
  $attributes['data-src'] = $attributes['src'];
  unset($attributes['src']);

  // Provide a default padding space for images, so that they take up the
  // correct amount of space on the screen to prevent reflow and
  // improve lazy loading.
  $path = $variables['path'];
  $public = file_create_url("public://");
  $path = str_replace($public, 'public://', $path);
  if (strpos($path, '?') !== FALSE) {
    $path = substr($path, 0, strpos($path, '?'));
  }
  $file_path = drupal_realpath($path);

  if (!empty($file_path)) {
    $size = image_get_info($file_path);
    if (isset($size['width']) && isset($size['height'])) {
      $attributes['width'] = $size['width'];
      $attributes['height'] = $size['height'];
      $ratio = round(($attributes['height'] / $attributes['width']) * 100, 2);
    }
  }
  if (isset($ratio)) {
    // Output the image.
    return '<div class="image-wrapper image-loading rs_preserve rs_skip" style="padding-bottom: ' . $ratio . '%">
      <div class="image"><img' . drupal_attributes($attributes) . ' /></div></div>';
  }
  else {
    // If we cannot find the image size, we just output a normal image with no
    // fancy lazy loading and reserved space.
    return '<img' . drupal_attributes($attributes) . ' />';
  }

}

/**
 * Returns HTML for a source tag.
 *
 * @param array $variables
 *   An associative array containing:
 *   - media: The media query to use.
 *   - src: Either the path of the image file (relative to base_path()) or a
 *     full URL.
 *   - dimensions: The width and height of the image (if known).
 *
 * @return string
 *   Output.
 *
 * @ingroup themeable
 */
function health_picture_source(array $variables) {

  $attributes['data-srcset'] = $variables['srcset'];

  if (isset($variables['media']) && !empty($variables['media'])) {
    $attributes['media'] = $variables['media'];
  }

  if (isset($variables['mime_type']) && !empty($variables['mime_type'])) {
    $attributes['type'] = $variables['mime_type'];
  }
  if (isset($variables['sizes']) && !empty($variables['sizes'])) {
    $attributes['sizes'] = $variables['sizes'];
  }

  return '<source' . drupal_attributes($attributes) . ' />';
}

/**
 * Implements hook_entity_view_mode_alter().
 */
function health_entity_view_mode_alter(&$view_mode, $context) {
  // Change display mode from 'inline' to 'inline_full' for infographics.
  if ($context['entity_type'] == 'node' && $context['entity']->type == 'publication' && $view_mode == 'inline') {
    $publication_type = field_get_items('node', $context['entity'], 'field_publication_type');
    if ($publication_type[0]['tid'] == PUBLICATION_TYPE_INFOGRAPHIC) {
      $view_mode = 'inline_large';
    }
  }
}

/**
 * Implements hook_metatag_metatags_view_alter().
 */
function health_metatag_metatags_view_alter(&$output, $instance, $options) {
  // Replace [theme-path] token in metatag output.
  foreach ($output as &$metatag) {
    if (isset($metatag['#attached']['drupal_add_html_head'])) {
      $value = &$metatag['#attached']['drupal_add_html_head'][0][0]['#value'];
      $value = str_replace(urlencode(THEME_PATH_TOKEN_GENERIC), path_to_theme(), $value);
    }
  }
}

/**
 * Implements hook_node_access_alter().
 *
 * Stolen from https://www.drupal.org/project/menu_view_unpublished.
 */
function health_query_node_access_alter(QueryAlterableInterface $query) {
  $c = &$query->conditions();
  // Remove the status condition if we suspect this query originates from
  // menu_tree_check_access().
  if ((count($c) == 4 || count($c) == 3) &&
    is_string($c[0]['field']) && $c[0]['field'] == 'n.status' &&
    is_string($c[1]['field']) && $c[1]['field'] == 'n.nid' && $c[1]['operator'] == 'IN'
  ) {
    unset($c[0]);
  }
}

/**
 * Implements theme_status_messages().
 *
 * Add role="alert".
 */
function health_status_messages($variables) {
  $display = $variables['display'];
  $output = '';
  $status_heading = array(
    'status' => t('Status message'),
    'error' => t('Error message'),
    'warning' => t('Warning message'),
  );
  foreach (drupal_get_messages($display) as $type => $messages) {
    if ($type == 'status') {
      $type = 'success';
    }
    $output .= "<div role=\"alert\" class=\"au-page-alerts au-page-alerts--{$type}\">\n";
    if (!empty($status_heading[$type])) {
      $output .= '<h2 class="element-invisible">' . $status_heading[$type] . "</h2>\n";
    }
    if (count($messages) > 1) {
      $output .= " <ul>\n";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>\n";
      }
      $output .= " </ul>\n";
    }
    else {
      $output .= reset($messages);
    }
    $output .= "</div>\n";
  }
  return $output;
}

/**
 * Implements theme_pager().
 *
 * Add au-pager classes.
 */
function health_pager($variables) {
  $tags = $variables['tags'];
  $element = $variables['element'];
  $parameters = $variables['parameters'];
  $quantity = $variables['quantity'];
  global $pager_page_array, $pager_total;

  // Calculate various markers within this pager piece:
  // Middle is used to "center" pages around the current page.
  $pager_middle = ceil($quantity / 2);

  // Current is the page we are currently paged to.
  $pager_current = $pager_page_array[$element] + 1;

  // First is the first page listed by this pager piece (re quantity).
  $pager_first = $pager_current - $pager_middle + 1;

  // Last is the last page listed by this pager piece (re quantity).
  $pager_last = $pager_current + $quantity - $pager_middle;

  // Max is the maximum page number.
  $pager_max = $pager_total[$element];

  // End of marker calculations.
  // Prepare for generation loop.
  $i = $pager_first;
  if ($pager_last > $pager_max) {

    // Adjust "center" if at end of query.
    $i = $i + ($pager_max - $pager_last);
    $pager_last = $pager_max;
  }
  if ($i <= 0) {

    // Adjust "center" if at start of query.
    $pager_last = $pager_last + (1 - $i);
    $i = 1;
  }

  // End of generation loop preparation.
  $li_first = theme('pager_first', array(
    'text' => isset($tags[0]) ? $tags[0] : t('« first'),
    'element' => $element,
    'parameters' => $parameters,
  ));
  $li_previous = theme('pager_previous', array(
    'text' => isset($tags[1]) ? $tags[1] : t('‹ previous'),
    'element' => $element,
    'interval' => 1,
    'parameters' => $parameters,
  ));
  $li_next = theme('pager_next', array(
    'text' => isset($tags[3]) ? $tags[3] : t('next ›'),
    'element' => $element,
    'interval' => 1,
    'parameters' => $parameters,
  ));
  $li_last = theme('pager_last', array(
    'text' => isset($tags[4]) ? $tags[4] : t('last »'),
    'element' => $element,
    'parameters' => $parameters,
  ));
  if ($pager_total[$element] > 1) {
    if ($li_first) {
      $items[] = array(
        'class' => array(
          'au-pager__item',
          'au-pager__item--first',
        ),
        'data' => $li_first,
      );
    }
    if ($li_previous) {
      $items[] = array(
        'class' => array(
          'au-pager__item',
          'au-pager__item--previous',
        ),
        'data' => $li_previous,
      );
    }

    // When there is more than one page, create the pager list.
    if ($i != $pager_max) {
      if ($i > 1) {
        $items[] = array(
          'class' => array(
            'au-pager__item',
            'au-pager__item--ellipsis',
          ),
          'data' => '…',
        );
      }

      // Now generate the actual pager piece.
      for (; $i <= $pager_last && $i <= $pager_max; $i++) {
        if ($i < $pager_current) {
          $items[] = array(
            'class' => array(
              'au-pager__item',
            ),
            'data' => theme('pager_previous', array(
              'text' => $i,
              'element' => $element,
              'interval' => $pager_current - $i,
              'parameters' => $parameters,
            )),
          );
        }
        if ($i == $pager_current) {
          $items[] = array(
            'class' => array(
              'au-pager__item',
              'au-pager__item--active',
            ),
            'data' => $i,
          );
        }
        if ($i > $pager_current) {
          $items[] = array(
            'class' => array(
              'au-pager__item',
            ),
            'data' => theme('pager_next', array(
              'text' => $i,
              'element' => $element,
              'interval' => $i - $pager_current,
              'parameters' => $parameters,
            )),
          );
        }
      }
      if ($i < $pager_max) {
        $items[] = array(
          'class' => array(
            'au-pager__item',
            'au-pager__item--ellipsis',
          ),
          'data' => '…',
        );
      }
    }

    // End generation.
    if ($li_next) {
      $items[] = array(
        'class' => array(
          'au-pager__item',
          'au-pager__item--next',
        ),
        'data' => $li_next,
      );
    }
    if ($li_last) {
      $items[] = array(
        'class' => array(
          'au-pager__item',
          'au-pager__item--last',
        ),
        'data' => $li_last,
      );
    }
    return '<div class="au-pager"><h2 class="sr-only">' . t('Pages') . '</h2>' . theme('item_list', array(
      'items' => $items,
      'attributes' => array(
        'class' => array(
          'au-link-list',
          'au-link-list--inline',
        ),
      ),
    )) . '</div>';
  }
}

function health_menu_local_tasks(&$variables) {
  $output = '';
  if (!empty($variables['primary'])) {
    $variables['primary']['#prefix'] = '<h2 class="element-invisible">' . t('Primary tabs') . '</h2>';
    $variables['primary']['#prefix'] .= '<ul class="health-tabs au-link-list au-link-list--inline primary">';
    $variables['primary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['primary']);
  }
  if (!empty($variables['secondary'])) {
    $variables['secondary']['#prefix'] = '<h2 class="element-invisible">' . t('Secondary tabs') . '</h2>';
    $variables['secondary']['#prefix'] .= '<ul class="health-tabs au-link-list au-link-list--inline secondary">';
    $variables['secondary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['secondary']);
  }
  return $output;
}

/**
 * Implements theme_item_list().
 *
 * Remove the div around item lists.
 */
function health_item_list($variables) {
  $items = $variables['items'];
  $title = $variables['title'];
  $type = $variables['type'];
  $attributes = $variables['attributes'];

  // Only output the list container and title, if there are any list items.
  // Check to see whether the block title exists before adding a header.
  // Empty headers are not semantic and present accessibility challenges.
  $output = '';
  if (isset($title) && $title !== '') {
    $output .= '<h3>' . $title . '</h3>';
  }
  if (!empty($items)) {
    $output .= "<{$type}" . drupal_attributes($attributes) . '>';
    $num_items = count($items);
    $i = 0;
    foreach ($items as $item) {
      $attributes = array();
      $children = array();
      $data = '';
      $i++;
      if (is_array($item)) {
        foreach ($item as $key => $value) {
          if ($key == 'data') {
            $data = $value;
          }
          elseif ($key == 'children') {
            $children = $value;
          }
          else {
            $attributes[$key] = $value;
          }
        }
      }
      else {
        $data = $item;
      }
      if (count($children) > 0) {

        // Render nested list.
        $data .= theme_item_list(array(
          'items' => $children,
          'title' => NULL,
          'type' => $type,
          'attributes' => $attributes,
        ));
      }
      if ($i == 1) {
        $attributes['class'][] = 'first';
      }
      if ($i == $num_items) {
        $attributes['class'][] = 'last';
      }
      $output .= '<li' . drupal_attributes($attributes) . '>' . $data . "</li>\n";
    }
    $output .= "</{$type}>";
  }
  return $output;
}

/**
 * Implements theme_textfield().
 *
 * Add Design system classes.
 */
function health_textfield($variables) {
  _form_set_class($variables['element'], array('au-text-input'));
  return theme_textfield($variables);
}

/**
 * Implements theme_password().
 *
 * Add Design system classes.
 */
function health_password($variables) {
  _form_set_class($variables['element'], array('au-text-input'));
  return theme_password($variables);
}

/**
 * Implements theme_textarea().
 *
 * Add Design system classes.
 */
function health_textarea($variables) {
  _form_set_class($variables['element'], array('au-text-input'));
  return theme_textarea($variables);
}

/**
 * Implements theme_select().
 *
 * Add Design system classes.
 */
function health_select($variables) {
  _form_set_class($variables['element'], array('au-select'));
  return theme_select($variables);
}

/**
 * Implements theme_button().
 *
 * Add Design system classes.
 */
function health_button($variables) {
  _form_set_class($variables['element'], array('au-btn', 'standard-gap'));
  return theme_button($variables);
}

/**
 * Implements theme_radio().
 *
 * Add Design system classes.
 * Output markup in format for Design system.
 */
function health_radio($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'radio';
  element_set_attributes($element, array('id', 'name', '#return_value' => 'value'));
  if (isset($element['#return_value']) && $element['#value'] !== FALSE && $element['#value'] == $element['#return_value']) {
    $element['#attributes']['checked'] = 'checked';
  }
  _form_set_class($element, array('au-control-input__input'));
  return '<label class="au-control-input au-control-input--block"><input' . drupal_attributes($element['#attributes']) . ' /><span class="au-control-input__text">'.$element['#title'].'</span></label>';
}

/**
 * Implements theme_checkbox().
 *
 * Add Design system classes.
 * Output markup in format for Design system.
 */
function health_checkbox($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'checkbox';
  element_set_attributes($element, array(
    'id',
    'name',
    '#return_value' => 'value',
  ));

  // Unchecked checkbox has #value of integer 0.
  if (!empty($element['#checked'])) {
    $element['#attributes']['checked'] = 'checked';
  }
  _form_set_class($element, array(
    'form-checkbox',
    'au-control-input__input'
  ));

  return '<label class="au-control-input"><input' . drupal_attributes($element['#attributes']) . ' /><span class="au-control-input__text">'.$element['#title'].'</span></label>';
}

/**
 * Implements theme_form_element().
 *
 * Don't show the label for radio and checkboxes.
 */
function health_form_element($variables) {

  $element = &$variables['element'];

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }
  // Add element's #type and #name as class to aid with JS/CSS selectors.
  if (in_array($element['#type'], ['radio', 'checkbox'])) {
    $element['#title_display'] = 'attribute';
  } else {
    $attributes['class'] = array('au-form__item');
  }


  if (!empty($element['#type'])) {
    $attributes['class'][] = 'au-form__item-type--' . strtr($element['#type'], '_', '-');
  }
  if (!empty($element['#name'])) {
    $attributes['class'][] = 'au-form__item-' . strtr($element['#name'], array(' ' => '-', '_' => '-', '[' => '-', ']' => ''));
  }
  // Add a class for disabled elements to facilitate cross-browser styling.
  if (!empty($element['#attributes']['disabled'])) {
    $attributes['class'][] = 'form-disabled';
  }
  $output = '<div' . drupal_attributes($attributes) . '>' . "\n";

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }
  $prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . $element['#field_prefix'] . '</span> ' : '';
  $suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . $element['#field_suffix'] . '</span>' : '';

  switch ($element['#title_display']) {
    case 'before':
    case 'invisible':
      $output .= ' ' . theme('form_element_label', $variables);
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;

    case 'after':
      $output .= ' ' . $prefix . $element['#children'] . $suffix;
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  if (!empty($element['#description'])) {
    $output .= '<div class="au-form__description">' . $element['#description'] . "</div>\n";
  }

  $output .= "</div>\n";

  return $output;
}

/**
 * Implements theme_webform_email().
 *
 * Add Design system classes.
 */
function health_webform_email($variables) {
  _form_set_class($variables['element'], array('au-text-input'));
  return theme_webform_email($variables);
}

/**
 * Implements theme_form().
 *
 * Add Design system classes.
 */
function health_form($variables) {
  if (key_exists('class', $variables['element']['#attributes'])) {
    if (is_array($variables['element']['#attributes']['class'])) {
      $variables['element']['#attributes']['class'][] = 'au-form';
    }
    else {
      $variables['element']['#attributes']['class'] .= ' au-form';
    }
  } else {
    $variables['element']['#attributes']['class'] = 'au-form';
  }
  return theme_form($variables);
}