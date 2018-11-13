<?php

/**
 * @file
 * This file contains the display suite preprocess hooks.
 *
 * They are called from health_preprocess_node.
 */

const HP_LINK_TOKEN = '[topic-hp-link]';
const GP_LINK_TOKEN = '[topic-gp-link]';

/**
 * DS - Content type.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_content_type(array &$variables) {
  $field_variables = _health_label_display($variables, 'content_type');

  // Get the list of content type names.
  $names = node_type_get_names();
  $name = $names[$variables['node']->type];
  // Do not display standard page.
  if ($name == 'Standard page') {
    $name = '';
  }
  $field_variables['items'] = [['#markup' => $name]];

  if ($field_variables['settings']['content_type']['label'] == 'inline') {

    $field_variables['ds-config'] = [
      'ow' => TRUE,
      'ow-el' => 'div',
      'ow-cl' => 'field row',
      'lb-cl' => 'field__label col-sm-3 col-md-2',
      'fi' => TRUE,
      'fi-cl' => 'field__item col-sm-9 col-md-10',
      'fi-el' => 'div'
    ];
    $field_variables['element'] = [
      '#label_display' => 'inline',
      '#entity_type' => 'node',
      '#object' => new stdClass(),
    ];
    return theme_ds_field_expert($field_variables);
  }

  return theme_field($field_variables);

}

/**
 * DS - Table of contents.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_table_of_content(array &$variables) {
  if ((isset($variables['field_generate_on_this_page'][LANGUAGE_NONE]) && $variables['field_generate_on_this_page'][LANGUAGE_NONE][0]['value'] == 1) ||
  (isset($variables['field_generate_in_collection'][LANGUAGE_NONE]) && $variables['field_generate_in_collection'][LANGUAGE_NONE][0]['value'] == 1)) {
    // This node is flagged to display table of content.
    $render_array = [
      '#theme' => 'toc',
    ];

    // Add full width indicator.
    $render_array['#full_width'] = FALSE;
    if (theme_get_setting('full_content_types')[$variables['type']] !== 0) {
      $render_array['#full_width'] = TRUE;
    }
    else {
      $render_array['#full_width'] = FALSE;
    }

    // Pass title text.
    if ($variables['field_generate_on_this_page'][LANGUAGE_NONE][0]['value'] == 1) {
      $render_array['#attached']['js'][] = [
        'data' => [
          'toc' => [
            'title' => t('On this page'),
          ],
        ],
        'type' => 'setting',
      ];
    }
    else {
      $render_array['#attached']['js'][] = [
        'data' => [
          'toc' => [
            'title' => t('In this collection'),
          ],
        ],
        'type' => 'setting',
      ];
    }

    return drupal_render($render_array);
  }
  return '';
}

/**
 * DS - Term link.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_term_link(array &$variables) {
  $para_item = $variables['paragraphs_item'];
  if ($para_item->bundle == 'para_taxonomy') {
    // Override term link field content with link field and term label.
    if (isset($para_item->field_related_term) && isset($para_item->field_link_external)) {
      $term = taxonomy_term_load($para_item->field_related_term[LANGUAGE_NONE][0]['target_id']);
      $term_label = $term->name;
      $link = $para_item->field_link_external[LANGUAGE_NONE][0]['url'];
      $render = array(
        '#theme' => 'link',
        '#text' => $term_label,
        '#path' => $link,
        '#options' => array(
          'attributes' => array(
            'class' => 'term-link',
            'title' => 'Term link',
          ),
          'html' => TRUE,
        ),
      );
      return drupal_render($render);
    }
  }
  return '';
}

/**
 * DS - Document accessibility form link.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_document_accessibility_form_link(array $variables) {
  // Find current page url.
  global $base_url;
  $current_url = drupal_encode_path($base_url . '/' . drupal_get_path_alias(current_path()));

  $render_array = [
    '#theme' => 'document_accessibility_link',
    '#current_page' => $current_url,
  ];

  return drupal_render($render_array);
}

/**
 * DS - para_blocks.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_para_rendered_blocks(array $variables) {
  $para_item = $variables['paragraphs_item'];
  $output = '';
  if (isset($para_item->field_para_block_id[LANGUAGE_NONE])) {
    $output = '<div class="row">';
    foreach ($para_item->field_para_block_id[LANGUAGE_NONE] as $block_delta) {
      $count = explode('-num', $block_delta['value'])[1];
      $block_delta['value'] = explode('-num', $block_delta['value'])[0];
      $block = module_invoke('bean', 'block_view', $block_delta['value']);
      $block['content']['bean'][$block_delta['value']]['num'] = $count;
      $output .= drupal_render($block['content']);
    }
    $output .= '</div>';
  }
  return $output;
}

/**
 * DS - Download image link.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_download_image_link(array &$variables) {
  $link_title = t("View text description and larger image of '!title' graphic", ['!title' => $variables['title']]);
  return l($link_title, 'node/' . $variables['nid'], ['html' => TRUE, 'attributes' => ['class' => ['print--hide']]]);
}

/**
 * DS - Download image link.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_view_video_link(array &$variables) {
  $link_title = t("More information about the video '!title'", ['!title' => $variables['title']]);
  return '<p>' . l($link_title, 'node/' . $variables['nid'], ['html' => TRUE, 'attributes' => ['class' => ['print--hide']]]) . '</p>';
}

/**
 * DS - Public HP switcher.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_public_hp_switcher(array &$variables) {
  if (arg(0) == 'node' && is_numeric(arg(1))) {
    // Check what is the current page for.
    $attached_node = node_load(arg(1));
    if ($attached_node->type == 'health_topic') {
      $render_elements = _health_get_render_elements_for_public_topic_page($variables);
    }
    elseif ($attached_node->type == 'health_topic_hp') {
      $render_elements = _health_get_render_elements_for_hp_topic_page($variables);
    }
    else {
      if (isset($attached_node->field_audience[LANGUAGE_NONE])) {
        if ($attached_node->field_audience[LANGUAGE_NONE][0]['tid'] == '451') {
          // This is a page for public.
          $render_elements = _health_get_render_elements_for_public_topic_page($variables);
        }
        else {
          // This is a page for health professionals.
          $render_elements = _health_get_render_elements_for_hp_topic_page($variables);
        }
      }

    }
  }

  if (isset($render_elements)) {
    $render_array = [
      '#theme' => 'public_hp_switcher',
      '#text' => $render_elements['text'],
      '#title' => $render_elements['title'],
      '#url' => $render_elements['url'],
    ];
    return drupal_render($render_array);
  }
  else {
    return '';
  }
}

/**
 * DS - Media query.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_media_enquiry(array &$variables) {
  $render_array = [
    '#theme' => 'media_enquiry',
  ];
  return drupal_render($render_array);
}

/**
 * DS - Publication collection.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_related_publication_collections(array &$variables) {
  // Find paragraph collections the current publication belong to.
  if ($variables['type'] == 'publication') {
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'paragraphs_item')
      ->entityCondition('bundle', 'publication_grouping')
      ->fieldCondition('field_reference_publication', 'target_id', $variables['nid'], '=')
      ->addMetaData('account', user_load(1));

    $result = $query->execute();
    if (isset($result['paragraphs_item'])) {
      $paragraphs_item_ids = array_keys($result['paragraphs_item']);
      $links = [];
      foreach ($paragraphs_item_ids as $paragraphs_item_id) {
        $node_query = new EntityFieldQuery();
        $node_query->entityCondition('entity_type', 'node')
          ->entityCondition('bundle', 'publication_collection')
          ->fieldCondition('field_publication_grouping', 'value', $paragraphs_item_id, '=')
          ->addMetaData('account', user_load(1));
        $node_result = $node_query->execute();
        if (isset($node_result['node'])) {
          $collection_ids = array_keys($node_result['node']);
          $collections = entity_load('node', $collection_ids);
          foreach ($collections as $collection) {
            $links[] = l($collection->title, $collection->path['alias']);
          }
        }
      }

      $list_render_array = [
        '#theme' => 'item_list',
        '#items' => $links,
        '#type' => 'ul',
      ];
      $output = drupal_render($list_render_array);

      $render_array = [
        '#theme' => 'publication_collection',
        '#collection_list' => $output,
      ];

      return drupal_render($render_array);
    }
  }
  return '';
}

/**
 * DS - Associated release.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_associated_release(array &$variables) {
  $field_variables = _health_label_display($variables, 'associated_release');

  // Load the view to get the associated release.
  $view = views_get_view('releases');
  $view->set_display('related_survey');
  $view->set_arguments([$variables['nid']]);
  $view->execute();
  if (!empty($view->result)) {
    $link = l($view->result[0]->node_title, 'node/' . $view->result[0]->nid);
    $field_variables['items'] = [['#markup' => $link]];
    if ($field_variables['settings']['associated_release']['label'] == 'inline') {
      $field_variables['classes'] .= ' field--inline--aligned';
    }
    return theme_field($field_variables);
  }
  return '';
}

/**
 * DS - Book navigation - Previous and next.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_book_navigation_prev_next(array &$variables) {
  if (isset($variables['content']['book_navigation'])) {
    return render($variables['content']['book_navigation']);
  }
  return '';
}

/**
 * DS - Recommendation tags.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_recommendation_tags(array &$variables) {
  // Grade.
  $items = [
    [
      'data' => $variables['content']['field_recommendation_grade'][0]['#markup'],
      'class' => ['au-recommendation__grade'],
    ],
  ];

  // Number.
  if (!empty($variables['content']['field_recommendation_number'])) {
    $items[] = ['data' => $variables['content']['field_recommendation_number'][0]['#markup']];
  }

  // Put them both in a tags list.
  return theme('item_list', [
    'items' => $items,
    'attributes' => ['class' => ['au-tags']],
    'type' => 'ul',
  ]);
}

/**
 * DS - Survey link (disabled)
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_survey_link_disabled(array &$variables) {
  if ($variables['field_status'][0]['value'] == 'Upcoming') {
    $friendly = _health_friendly_interval_date($variables['field_date_start'][LANGUAGE_NONE][0]['value']);
    return "<button class='au-btn' disabled='disabled'>Survey will open {$friendly['text']}</button>";
  }
  elseif ($variables['field_status'][0]['value'] == 'Closed') {
    return "<button class='au-btn' disabled='disabled'>Survey is closed</button>";
  }
  return '';
}

/**
 * DS - Start end date friendly.
 *
 * Only works if this node has start and end dates.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_start_end_date_friendly(array &$variables) {
  $field_variables = _health_label_display($variables, 'start_end_date_friendly');
  $start = _health_friendly_interval_date($variables['field_date_start'][LANGUAGE_NONE][0]['value']);
  if ($start['ago'] == FALSE) {
    $field_variables['label'] = 'Opens in';
    $field_variables['items'] = [['#markup' => $start['text']]];
      if ($field_variables['settings']['content_type']['label'] == 'inline') {
        $field_variables['ds-config'] = [
          'ow' => TRUE,
          'ow-el' => 'div',
          'ow-cl' => 'field row',
          'lb-cl' => 'field__label col-sm-3 col-md-2',
          'fi' => TRUE,
          'fi-cl' => 'field__item col-sm-9 col-md-10',
          'fi-el' => 'div'
        ];
        $field_variables['element'] = [
          '#label_display' => 'inline',
          '#entity_type' => 'node',
          '#object' => new stdClass(),
        ];
        return theme_ds_field_expert($field_variables);
    }
    return theme_field($field_variables);
  }

  $end = _health_friendly_interval_date($variables['field_date_end'][LANGUAGE_NONE][0]['value']);
  if ($end['ago'] == FALSE) {
    $field_variables['label'] = 'Time remaining';
    $field_variables['items'] = [['#markup' => $end['text']]];
    if ($field_variables['settings']['start_end_date_friendly']['label'] == 'inline') {
      $field_variables['ds-config'] = [
        'ow' => TRUE,
        'ow-el' => 'div',
        'ow-cl' => 'field row',
        'lb-cl' => 'field__label col-sm-3 col-md-2',
        'fi' => TRUE,
        'fi-cl' => 'field__item col-sm-9 col-md-10',
        'fi-el' => 'div'
      ];
      $field_variables['element'] = [
        '#label_display' => 'inline',
        '#entity_type' => 'node',
        '#object' => new stdClass(),
      ];
      return theme_ds_field_expert($field_variables);
    }
    return theme_field($field_variables);
  }
  return '';
}

/**
 * DS - Close button for alerts.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_alert_close_button(array &$variables) {
  return theme('health_alert_close_button', ['nid' => $variables['nid']]);
}

/**
 * DS - Close button for alerts.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_separator(array &$variables) {
  return theme('health_separator');
}

/**
 * DS - Static google map using long and latitude.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_google_maps_static(array &$variables) {
  $gmaps_api = theme_get_setting('google_maps_api');
  if ($gmaps_api) {
    if (isset($variables['field_location_latitude']) && isset($variables['field_location_longitude'])) {
      $lat = $variables['field_location_latitude'][0]['value'];
      $long = $variables['field_location_longitude'][0]['value'];
      $src = GOOGLE_MAP_API . '?center=' . $lat . ',' . $long . '&zoom=13&size=400x400&maptype=roadmap&key=' . $gmaps_api;
      $src .= '&markers=color:red%7C' . $lat . ',' . $long;
      return '<img src="' . $src . '" />';
    }
  }
  return '';
}

/**
 * DS - Static google map using long and latitude.
 *
 * @param array $variables
 *   Variables from preprocess.
 *
 * @return string
 *   HTML output.
 */
function health_preprocess_ds_google_maps_interactive(array &$variables) {
  $gmaps_client = theme_get_setting('google_maps_client');
  if ($gmaps_client) {

    if (isset($variables['field_location_latitude']) && isset($variables['field_location_longitude'])) {

      $lat = $variables['field_location_latitude'][0]['value'];
      $long = $variables['field_location_longitude'][0]['value'];

      drupal_add_js('https://maps.googleapis.com/maps/api/js?client=' . $gmaps_client . '&callback=initGoogleMaps',
        ['type' => 'external', 'defer' => 'defer', 'async' => 'async']
      );

      drupal_add_js([
        'health' => [
          'maps' => [
            'google' => [
              [
                'long' => floatval($long),
                'lat' => floatval($lat),
                'id' => 'google-map-' . $variables['id']
              ]
            ]
          ],
        ],
      ], 'setting');

      drupal_add_js(path_to_theme() . '/js/dist/health.maps.min.js', 'file');

      return '<div id="google-map-' . $variables['id'] . '" class="field health-maps health-maps--google"></div>';
    }
  }
  return '';
}


