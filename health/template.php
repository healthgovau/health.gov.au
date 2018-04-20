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
 * Implements HOOK_theme().
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
    'variables' => [],
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
      'path' => NULL
    ],
    'template' => 'section_link',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['statistic_value'] = [
    'variables' => [
      'prefix' => NULL,
      'value' => NULL,
      'suffix' => NULL
    ],
    'template' => 'statistic_value',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['statistic_trend_icon'] = [
    'variables' => [
      'direction' => NULL,
      'indication' => NULL
    ],
    'template' => 'statistic_trend_icon',
    'path' => drupal_get_path('theme', 'health') . '/templates/health_templates',
  ];

  $theme['health_breadcrumb'] = [
    'variables' => [
      'title' => NULL,
      'breadcrumb' => array()
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
 * Implement hook_html_head_alter().
 */
function health_html_head_alter(&$head) {
  // Simplify the meta tag for character encoding.
  if (isset($head['system_meta_content_type']['#attributes']['content'])) {
    $head['system_meta_content_type']['#attributes'] = array('charset' => str_replace('text/html; charset=', '', $head['system_meta_content_type']['#attributes']['content']));
  }
}

/**
 * Implements hook_page_alter().
 *
 * Look for the last block in the region. This is impossible to determine from
 * within a preprocess_block function.
 *
 * @param $page
 *   Nested array of renderable elements that make up the page.
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
        ]
      );
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
          ]
        );
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
        'selected_filters' => theme_item_list(['items' => $links, 'type' => 'ul', 'attributes'=>['class' => 'au-tags'], 'title'=>'']),
        'clear_all' => $clear_all,
      ]);
    }
  }
}

/**
* Implements hook_form_alter().
* @param $form
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
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function health_form_search_api_page_search_form_alter(&$form, &$form_state) {
  // Add wrapper to apply the uikit search form style.
  $form['#prefix'] = '<div class="block block-search-api-page contextual-links-region last even" id="search-api-page-search-form">';
  $form['#suffix'] = '</div>';
  $form['keys_1']['#attributes']['placeholder'] = t('Enter your search term');
}

/**
 * Implements THEME_form_element_lable().
 *
 * Alters the checkbox and radio buttons so the markup is usable for the uikit.
 */
function health_form_element_label($variables) {
  $element = $variables['element'];
  // This is also used in the installer, pre-database setup.
  $t = get_t();

  // If title and required marker are both empty, output no label.
  if ((!isset($element['#title']) || $element['#title'] === '') && empty($element['#required'])) {
    return '';
  }

  // If the element is required, a required marker is appended to the label.
  $required = !empty($element['#required']) ? theme('form_required_marker', array('element' => $element)) : '';
  $title = key_exists('#title', $element) ? filter_xss_admin($element['#title']) : '';

  $attributes = array();
  // Show label only to screen readers to avoid disruption in visual flows.
  if ($element['#title_display'] == 'invisible') {
    $attributes['class'] = 'element-invisible';
  }

  if (!empty($element['#id'])) {
    $attributes['for'] = $element['#id'];
  }

  $output = '';

  // Find out what type of form element this is.
  $type = !empty($element['#type']) ? $element['#type'] : FALSE;
  $checkbox = $type && $type === 'checkbox';
  $radio = $type && $type === 'radio';

  // Construct the title.
  $title = $t('!title !required', array('!title' => $title, '!required' => $required));

  if ($checkbox || $radio) {
    // Checkboxes and radios need a span around them to support UI kit styling.
    $output .= $element['#children'];
    $output .= '<span class="input__text">';
    $output .= $title;
    $output .= '</span>';
  } else {
    // The leading whitespace helps visually separate fields from inline labels.
    $output = $title;
  }
  return ' <label' . drupal_attributes($attributes) . '>' . $output . "</label>\n";
}

/**
 * Implements THEME_form_element().
 *
 * Alters the checkbox and radio buttons so the markup is usable for the uikit.
 */
function health_form_element(&$variables) {
  $element = &$variables['element'];
  $name = !empty($element['#name']) ? $element['#name'] : FALSE;
  $type = !empty($element['#type']) ? $element['#type'] : FALSE;
  $checkbox = $type && $type === 'checkbox';
  $radio = $type && $type === 'radio';

  // Create an attributes array for the wrapping container.
  if (empty($element['#wrapper_attributes'])) {
    $element['#wrapper_attributes'] = array();
  }
  $wrapper_attributes = &$element['#wrapper_attributes'];

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  // Add wrapper ID for 'item' type.
  if ($type && $type === 'item' && !empty($element['#markup']) && !empty($element['#id'])) {
    $wrapper_attributes['id'] = $element['#id'];
  }

  // Check for errors and set correct error class.
  if ((isset($element['#parents']) && form_get_error($element) !== NULL) || (!empty($element['#required']))) {
    $wrapper_attributes['class'][] = 'has-error';
  }

  // Add necessary classes to wrapper container.
  $wrapper_attributes['class'][] = 'form-item';
  if ($name) {
    $wrapper_attributes['class'][] = 'form-item-' . drupal_html_class($name);
  }
  if ($type) {
    $wrapper_attributes['class'][] = 'form-type-' . drupal_html_class($type);
  }
  if (!empty($element['#attributes']['disabled'])) {
    $wrapper_attributes['class'][] = 'form-disabled';
  }
  if (!empty($element['#autocomplete_path']) && drupal_valid_path($element['#autocomplete_path'])) {
    $wrapper_attributes['class'][] = 'form-autocomplete';
  }

  // Checkboxes and radios do no receive the 'form-group' class, instead they
  // simply have their own classes.
  if ($checkbox || $radio) {
    $wrapper_attributes['class'][] = drupal_html_class($type);
  }
  elseif ($type && $type !== 'hidden') {
    $wrapper_attributes['class'][] = 'form-group';
  }

  // Create a render array for the form element.
  $build = array(
    '#theme_wrappers' => array('container__form_element'),
    '#attributes' => $wrapper_attributes,
  );

  // Render the label for the form element.
  $build['label'] = array(
    '#markup' => theme('form_element_label', $variables),
    '#weight' => $element['#title_display'] === 'before' ? 0 : 2,
  );

  // Checkboxes and radios render the input element inside the label. If the
  // element is neither of those, then the input element must be rendered here.
  if (!$checkbox && !$radio) {
    $prefix = isset($element['#field_prefix']) ? $element['#field_prefix'] : '';
    $suffix = isset($element['#field_suffix']) ? $element['#field_suffix'] : '';
    if ((!empty($prefix) || !empty($suffix)) && (!empty($element['#input_group']) || !empty($element['#input_group_button']))) {
      if (!empty($element['#field_prefix'])) {
        $prefix = '<span class="input-group-' . (!empty($element['#input_group_button']) ? 'btn' : 'addon') . '">' . $prefix . '</span>';
      }
      if (!empty($element['#field_suffix'])) {
        $suffix = '<span class="input-group-' . (!empty($element['#input_group_button']) ? 'btn' : 'addon') . '">' . $suffix . '</span>';
      }

      // Add a wrapping container around the elements.
      $input_group_attributes['class'][] = 'input-group';
      $prefix = '<div' . drupal_attributes($input_group_attributes) . '>' . $prefix;
      $suffix .= '</div>';
    }

    // Build the form element.
    $build['element'] = array(
      '#markup' => $element['#children'],
      '#prefix' => !empty($prefix) ? $prefix : NULL,
      '#suffix' => !empty($suffix) ? $suffix : NULL,
      '#weight' => 1,
    );
  }

  // Construct the element's description markup.
  if (!empty($element['#description'])) {
    $build['description'] = array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => array('help-block'),
      ),
      '#weight' => isset($element['#description_display']) && $element['#description_display'] === 'before' ? 0 : 20,
      0 => array('#markup' => filter_xss_admin($element['#description'])),
    );
  }

  // Render the form element build array.
  return drupal_render($build);
}

/**
 * Implements THEME_select()
 *
 * Wraps a div around the select element so we can use the :after attribute to consistently style the element.
 */
function health_select($variables) {
  $element = $variables['element'];
  element_set_attributes($element, array('id', 'name', 'size'));
  _form_set_class($element, array('form-select'));

  return '<div class="form-select--wrapper"><select' . drupal_attributes($element['#attributes']) . '>' . form_select_options($element) . '</select></div>';
}

/**
 * Implements hook_js_alter().
 *
 * Perform necessary alterations to the JavaScript before it is presented on the page.
 *
 * @param array $javascript
 *   An array of all JavaScript being presented on the page.
 */
function health_js_alter(&$javascript) {
  // Replace jQuery version on non admin pages.
  $replace_jquery = TRUE;

  // Array of additional admin paths
  $extra_admin_paths = array(
    'node/*/edit',
    'node/add/*',
    'media/browser*',
    'media/ajax*'
  );

  // Do not apply to admin pages that may require lower version of jQuery provided by Drupal core
  if (path_is_admin(current_path())) {
    $replace_jquery = FALSE;
  } else {
    foreach ($extra_admin_paths as $extra_admin_path) {
      if (drupal_match_path(current_path(), $extra_admin_path)) {
        $replace_jquery = FALSE;
      }
    }
  }

  if ($replace_jquery) {
    $javascript['misc/jquery.js']['data'] = 'https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js';
    $javascript['misc/jquery.js']['type'] = 'external';
  }

  // Move all JS to the footer, to improve page load.
  foreach($javascript as $key => $js) {
    $javascript[$key]['scope'] = 'footer';
  }

  // Remove unused js.
  unset($javascript['profiles/govcms/modules/contrib/field_group/field_group.js']);
  unset($javascript['profiles/govcms/modules/contrib/superfish/superfish.js']);
  unset($javascript['profiles/govcms/libraries/superfish/superfish.js']);
  unset($javascript['profiles/govcms/libraries/superfish/supersubs.js']);
  unset($javascript['profiles/govcms/libraries/superfish/supposition.js']);
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
  if (key_exists('profiles/govcms/modules/contrib/ds/layouts/ds_2col/ds_2col.css', $css)) {
    $css['profiles/govcms/modules/contrib/ds/layouts/ds_2col/ds_2col.css']['browsers'] = array('IE' => 'gt IE 8');
  }
  if (key_exists('profiles/govcms/modules/contrib/ds/layouts/ds_2col_stacked/ds_2col_stacked.css', $css)) {
    $css['profiles/govcms/modules/contrib/ds/layouts/ds_2col_stacked/ds_2col_stacked.css']['browsers'] = array('IE' => 'gt IE 8');
  }
}

/**
 * Implements hook_file_entity_download_link().
 *
 * Output differently for Image content type.
 *
 * @param $variables
 *
 * @return string
 */
function health_file_entity_download_link($variables) {

  $nid = arg(1);
  if (is_numeric($nid)) {
    $node = node_load($nid);

    // If this is not an image or publication content type, do the normal formatting.
    if ($node->type != 'image' && $node->type != 'publication') {
      return theme_file_entity_download_link($variables);
    } else {

      // Grab the file.
      $file = $variables['file'];

      // Get file title to use in analytics and accessibility.
      // Default is the resource title.
      $title = $node->title;

      // Publications.
      if ($node->type == 'publication') {
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
                  if (count($docs) > 1) { // Multiple document parts.
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

      // Images.
      if ($node->type == 'image') {
        $docs = $node->field_image_files[$node->language];
        foreach ($docs as $doc) {
          $entities = entity_load('paragraphs_item', [$doc['value']]);
          if (!empty($entities)) {
            $para_documents = array_pop($entities);
            foreach($para_documents->field_images[LANGUAGE_NONE] as $image) {
              if ($image['fid'] == $file->fid) {
                // Get sizing.
                if (isset($para_documents->field_image_size) && !empty($para_documents->field_image_size)) {
                  $size = $para_documents->field_image_size[LANGUAGE_NONE][0]['value'];
                }
              }
            }
          }
        }
      }

      // Construct the link.
      $variables['text'] = '<div class="file__link">Download <span class="file__link-title">' . $title . ' as</span> ' . health_get_friendly_mime($file->filemime) . '</div>';

      // Add metatdata (file size, image size, no of pages)

      // Round to 1 decimal for MB and whole number for KB in terms of the file size format.
      $file_size = explode(' ', format_size($file->filesize));
      if (isset($file_size[1])) {
        if ($file_size[1] == 'MB') {
          $formatted_filesize = round($file_size[0], 1) . ' ' . $file_size[1];
        }
        else {
          $formatted_filesize = round($file_size[0], 0) . ' ' . $file_size[1];
        }
      }

      $variables['text'].= '<span class="file__meta"> - ' . $formatted_filesize;
      if (isset($no_of_pages)) {
        $variables['text'].= ', ' . $no_of_pages . ' page';
        if ($no_of_pages > 1) {
          $variables['text'].= 's';
        }
      }
      if (isset($size)) {
        $variables['text'].= ', ' . $size;
      }
      $variables['text'].= '</span>';

      // Get the icon.
      $icon_directory = $variables['icon_directory'];
      $icon = theme('file_icon', array('file' => $file, 'icon_directory' => $icon_directory));

      // Get the path to the file.
      $uri = file_entity_download_uri($file);

      // Set options as per anchor format described at
      // http://microformats.org/wiki/file-format-examples
      $uri['options']['attributes']['type'] = $file->filemime . '; length=' . $file->filesize;
      $uri['options']['html'] = TRUE;

      // Add filename attribute for analytics.
      $uri['options']['attributes']['data-filename'] = $title;
      $uri['options']['attributes']['data-filetype'] = $file->filemime;

      // Output the link.
      $output = '<span class="file"> ' . $icon . ' ' . l($variables['text'], $uri['path'], $uri['options']) . '</span>';

      return $output;
    }
  }

  return theme_file_entity_download_link($variables);

}

/**
 * Implements theme_webform_element().
 *
 * @param $variables
 *
 * @return string
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
    $type = (isset($element['#type']) && !in_array($element['#type'], array('markup', 'textfield', 'webform_email', 'webform_number'))) ? $element['#type'] : $element['#webform_component']['type'];
  }

  // Convert the parents array into a string, excluding the "submitted" wrapper.
  $nested_level = $element['#parents'][0] == 'submitted' ? 1 : 0;
  $parents = str_replace('_', '-', implode('--', array_slice($element['#parents'], $nested_level)));

  $wrapper_attributes = isset($element['#wrapper_attributes']) ? $element['#wrapper_attributes'] : array('class' => array());
  $wrapper_classes = array(
    'form-item',
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
    $description = ' <div class="description">' . $element['#description'] . "</div>\n";
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
 * Implements theme_menu_link().
 * Add default audience filter to specific paths.
 *
 * @param array $variables
 *
 * @return string
 */
function health_menu_link(array $variables) {
  if ($query = _health_default_audience_menu($variables['element']['#href'])) {
    $variables['element']['#localized_options']['query'] = $query;
  }
  return theme_menu_link($variables);
}

/**
 * Implements theme_superfish_menu_item_link().
 * Add default audience filter to specific paths.
 *
 * @param $variables
 *
 * @return string
 */
function health_superfish_menu_item_link($variables) {
  if ($query = _health_default_audience_menu($variables['menu_item']['link']['href'])) {
    $variables['link_options']['query'] = $query;
  }
  return theme_superfish_menu_item_link($variables);
}

/**
 * Implements theme_crumbs_breadcrumb_link().
 * Add default audience filter to specific paths.
 *
 * @param array $item
 *
 * @return string
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
}

/**
 * Implements theme_image();
 * 
 * @param $variables
 *
 * @return bool|string
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
  if (!key_exists('alt', $attributes)) {
    $attributes['alt'] = '';
  }

  // Set data-src instead of src for lazy loading.
  $attributes['data-src'] = $attributes['src'];
  unset($attributes['src']);

  // Provide a default padding space for images, so that they take up the correct
  // space on the screen to prevent reflow and improve lazy loading.
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
      $ratio = round(($attributes['height'] / $attributes['width']) * 100);
    }
  }
  if (isset($ratio)) {
    // Output the image.
    return '<div class="image-wrapper image-loading rs_preserve rs_skip" style="padding-bottom: ' . $ratio . '%">
      <div class="image"><img' . drupal_attributes($attributes) . ' /></div></div>';
  } else {
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
 *
 * @param $view_mode
 * @param $context
 */
function health_entity_view_mode_alter(&$view_mode, $context) {
  // Change display mode from 'inline' to 'inline_full' for infographics
  if ($context['entity_type'] == 'node' && $context['entity']->type == 'publication' && $view_mode == 'inline') {
    $publication_type = field_get_items('node', $context['entity'], 'field_publication_type');
    if ($publication_type[0]['tid'] == PUBLICATION_TYPE_INFOGRAPHIC) {
      $view_mode = 'inline_large';
    }
  }
}

/**
 * Alter metatags before being cached.
 *
 * This hook is invoked prior to the meta tags for a given page are cached.
 *
 * @param array $output
 *   All of the meta tags to be output for this page in their raw format. This
 *   is a heavily nested array.
 * @param string $instance
 *   An identifier for the current page's page type, typically a combination
 *   of the entity name and bundle name, e.g. "node:story".
 * @param array $options
 *   All of the options used to generate the meta tags.
 */
function health_metatag_metatags_view_alter(&$output, $instance, $options) {
  // Replace [theme-path] token in metatag output.
  foreach($output as &$metatag) {
    if (isset($metatag['#attached']['drupal_add_html_head'])) {
      $value = &$metatag['#attached']['drupal_add_html_head'][0][0]['#value'];
      $value = str_replace(urlencode(THEME_PATH_TOKEN_GENERIC), path_to_theme(), $value);
    }
  }
}
