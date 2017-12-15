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
  // Get the list of content type names.
  $names = node_type_get_names();
  $label = $names[$variables['node']->type];

  // Setup the field.
  $field_variables = [
    'label_hidden' => FALSE,
    'title_attributes' => '',
    'content_attributes' => '',
    'item_attributes' => [0 => ''],
    'attributes' => '',
    'classes' => 'field-type-taxonomy-term-reference',
    'items' => [
      [
        '#markup' => $label
      ]
    ],
  ];

  $field_variables = _health_label_display($variables, 'content_type', $field_variables);

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
  if (isset($variables['field_generate_on_this_page'][LANGUAGE_NONE]) && $variables['field_generate_on_this_page'][LANGUAGE_NONE][0]['value'] == 1) {
    // This node is flagged to display table of content.
    drupal_add_js(drupal_get_path('theme', 'health') . '/js/anchorific.js');
    drupal_add_js(drupal_get_path('theme', 'health') . '/js/health.toc.js');

    return theme('toc', []);
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

  return theme('document_accessibility_link', ['current_page' => $current_url]);
}

/**
 * Implements DS preprocess field -- date published.
 *
 * @param $variable
 *
 * @return string
 */
function health_preprocess_ds_date_published(&$variables) {
  $date = _health_find_first_publish_date($variables['nid']);
  $date_render = [
    'date' => $date,
    'attributes' => [
      'content' => strtotime($date),
      'property' => 'dc:date',
    ],
  ];

  // Setup the field.
  $field_variables = [
    'label_hidden' => FALSE,
    'title_attributes' => '',
    'content_attributes' => '',
    'item_attributes' => [0 => ''],
    'attributes' => '',
    'classes' => 'field-name-field-date-published field-type-datetime',
    'items' => [
      [
        '#markup' => theme('date_display_single', $date_render),
      ]
    ],
  ];
  $field_variables = _health_label_display($variables, 'date_published', $field_variables);
  $field_variables['classes'] .= ' field-name-field-date-published';

  return theme_field($field_variables);
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
 * @param $hook
 *
 * @return string
 */
function health_preprocess_ds_download_image_link(&$variables) {
  return l('Download, share or print ' . $variables['title'], 'node/' . $variables['nid']);
}

/**
 * Display suite preprocessor - public hp switcher.
 *
 * @param $variables
 * @return string
 */
function health_preprocess_ds_public_hp_switcher(&$variables) {
  // Check what is the current page for.
  $attached_node = node_load(arg(1));
  if ($attached_node->type == 'health_topic') {
    $render_elements = _health_get_render_elements_for_public_topic_page($variables, TRUE);
  }
  else if ($attached_node->type == 'health_topic_hp') {
    $render_elements = _health_get_render_elements_for_hp_topic_page($variables, TRUE);
  }
  else {
    if (isset($attached_node->field_audience[LANGUAGE_NONE])) {
      if ($attached_node->field_audience[LANGUAGE_NONE][0]['tid'] == '451') {
        // This is a page for public.
        $render_elements = _health_get_render_elements_for_public_topic_page($variables, FALSE);
      }
      else {
        // This is a page for health professionals.
        $render_elements = _health_get_render_elements_for_hp_topic_page($variables, FALSE);
      }
    }

  }

  if (isset($render_elements)) {
    return theme('public_hp_switcher', $render_elements);
  }
  else {
    // Should not have this case.
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
  return theme('media_enquiry', ['nid' => $variables['node']->nid]);
}