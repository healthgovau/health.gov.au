<?php

/**
 * This file contains the display suite preprocess hooks.
 * They are called from health_preprocess_node.
 */

CONST HP_LINK_TOKEN = '[topic-hp-link]';
CONST GP_LINK_TOKEN = '[topic-gp-link]';

/**
 * DS - Content type.
 *
 * @param $variables
 *
 * @return string
 */
function health_preprocess_ds_content_type(&$variables) {
  $field_variables = _health_label_display($variables, 'content_type');

  // Get the list of content type names.
  $names = node_type_get_names();
  $name = $names[$variables['node']->type];
  // Do not display standard page.
  if ($name == 'Standard page') {
    $name = '';
  }
  $field_variables['items'] = [['#markup' => $name]];

  return theme_field($field_variables);
}

/**
 * Implements DS preprocess field -- table of content.
 *
 * @param $variables
 * 
 * @return string
 */
function health_preprocess_ds_table_of_content(&$variables) {
  if ((isset($variables['field_generate_on_this_page'][LANGUAGE_NONE]) && $variables['field_generate_on_this_page'][LANGUAGE_NONE][0]['value'] == 1) ||
  (isset($variables['field_generate_in_collection'][LANGUAGE_NONE]) && $variables['field_generate_in_collection'][LANGUAGE_NONE][0]['value'] == 1)) {
    // This node is flagged to display table of content.
    $render_array = [
      '#theme' => 'toc',
    ];

    $render_array['#attached']['js'][] = [
      'data' => drupal_get_path('theme', 'health') . '/js/libraries/anchorific.min.js',
      'type' => 'file',
    ];
    $render_array['#attached']['js'][] = [
      'data' => drupal_get_path('theme', 'health') . '/js/dist/health.toc.min.js',
      'type' => 'file',
    ];

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
}

/**
 * Implements DS preprocess field -- term link.
 *
 * @param $variables
 *
 * @return string
 */
function health_preprocess_ds_term_link(&$variables) {
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
}

/**
 * Implements DS preprocess field -- document accessibility form link.
 *
 * @param $variables
 *
 * @return string
 */
function health_preprocess_ds_document_accessibility_form_link($variables) {
  // Find current page url.
  Global $base_url;
  $current_url = drupal_encode_path($base_url . '/' . drupal_get_path_alias(current_path()));

  $render_array = [
    '#theme' => 'document_accessibility_link',
    '#current_page' => $current_url,
  ];

  return drupal_render($render_array);
}

/**
 * Implements DS preprocess field -- para_blocks.
 *
 * @param $variable
 *
 * @return String
 */
function health_preprocess_ds_para_rendered_blocks($variable) {
  $para_item = $variable['paragraphs_item'];
  $output = '';
  if (isset($para_item->field_para_block_id[LANGUAGE_NONE])) {
    foreach ($para_item->field_para_block_id[LANGUAGE_NONE] as $block_delta) {
      $block = block_load('bean', $block_delta['value']);
      $block_render = _block_render_blocks(array($block));
      $block_renderable_array = _block_get_renderable_array($block_render);
      $output .= drupal_render($block_renderable_array);
    }
  }
  return $output;
}

/**
 * Display suite preprocessor - download image link.
 *
 * @param $variables
 *
 * @return string
 */
function health_preprocess_ds_download_image_link(&$variables) {
  return l("View text version and larger image of '" . $variables['title'] . "' graphic", 'node/' . $variables['nid'], ['html' => true]);
}

/**
 * Display suite preprocessor - public hp switcher.
 *
 * @param $variables
 *
 * @return string
 */
function health_preprocess_ds_public_hp_switcher(&$variables) {
  if (arg(0) == 'node' && is_numeric(arg(1))) {
    // Check what is the current page for.
    $attached_node = node_load(arg(1));
    if ($attached_node->type == 'health_topic') {
      $render_elements = _health_get_render_elements_for_public_topic_page($variables);
    }
    else if ($attached_node->type == 'health_topic_hp') {
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
 * Display suite preprocessor - media query.
 *
 * @param $variables
 *
 * @return string
 */
function health_preprocess_ds_media_enquiry(&$variables) {
  $render_array = [
    '#theme' => 'media_enquiry',
  ];
  return drupal_render($render_array);
}

/**
 * Display suite preprocessor -- publication collection
 *
 * @param $variables
 *
 * @return string
 */
function health_preprocess_ds_related_publication_collections(&$variables) {
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
}

/**
 * Display suite preprocessor -- associated release
 *
 * @param $variables
 *
 * @return string
 */
function health_preprocess_ds_associated_release(&$variables) {
  $field_variables = _health_label_display($variables, 'associated_release');

  // Load the view to get the associated release.
  $view = views_get_view('releases');
  $view->set_display('related_survey');
  $view->set_arguments([$variables['nid']]);
  $view->execute();
  if (!empty($view->result)) {
    $link = l($view->result[0]->node_title, 'node/' . $view->result[0]->nid);
    $field_variables['items'] = [['#markup'=> $link]];
    return theme_field($field_variables);
  }
  return '';
}

/**
 * DS - Content type.
 *
 * @param $variables
 *
 * @return string
 */
function health_preprocess_ds_book_navigation_prev_next(&$variables) {
  if (isset($variables['content']['book_navigation'])) {
    return render($variables['content']['book_navigation']);
  }
}

/**
 * DS - Recommendation tags.
 *
 * @param $variables
 *
 * @return string
 */
function health_preprocess_ds_recommendation_tags(&$variables) {
  // Grade
  $items = [[
    'data' => $variables['content']['field_recommendation_grade'][0]['#markup'],
    'class' => ['recommendation__grade']
  ]];
  // Number
  if (!empty($variables['content']['field_recommendation_number'])) {
    $items[] = ['data' => $variables['content']['field_recommendation_number'][0]['#markup']];
  }
  // Put them both in a tags list.
  return theme('item_list',[
    'items' => $items,
    'attributes' => ['class' => ['au-tags']],
    'type' => 'ul'
  ]);
}

/**
 * DS - Survey link (disabled)
 *
 * @param $variables
 *
 * @return string
 */
function health_preprocess_ds_survey_link_disabled(&$variables) {
  if ($variables['field_status'][0]['value'] == 'Upcoming') {
    $friendly = _health_friendly_interval_date($variables['field_date_start'][LANGUAGE_NONE][0]['value']);
    return "<button class='button' disabled='disabled'>Survey will open in {$friendly['text']}</button>";
  } else if ($variables['field_status'][0]['value'] == 'Closed') {
    return "<button class='button' disabled='disabled'>Survey is closed</button>";
  }
}

/**
 * DS - Start end date friendly
 * Only works if this node has start and end dates.
 *
 * @param $variables
 *
 * @return string
 */
function health_preprocess_ds_start_end_date_friendly(&$variables) {
  $field_variables = _health_label_display($variables, 'start_end_date_friendly');
  $start = _health_friendly_interval_date($variables['field_date_start'][LANGUAGE_NONE][0]['value']);
  if ($start['ago'] == FALSE) {
    $field_variables['label'] = 'Opens in';
    $field_variables['items'] = [['#markup' => $start['text']]];
    return theme_field($field_variables);
  }

  $end = _health_friendly_interval_date($variables['field_date_end'][LANGUAGE_NONE][0]['value']);
  if ($end['ago'] == FALSE) {
    $field_variables['label'] = 'Time remaining';
    $field_variables['items'] = [['#markup' => $end['text']]];
    return theme_field($field_variables);
  }
}
