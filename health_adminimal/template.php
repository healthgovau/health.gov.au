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

/**
 * Implement THEME_toc_filter().
 */
function health_adminimal_toc_filter($variables) {
  $output = '';
  $output .= '<nav class="index-links">';
  $output .= '<h2 id="index-links">' . t('In this section') . '</h2>';
  $output .= $variables['content'];
  $output .= '</nav>';
  return $output;
}

/**
 * Implements THEME_toc_filter_back_to_top().
 */
function health_adminimal_toc_filter_back_to_top($variables) {
  return '<span class="back-to-index-link"><a href="#index-links">' . t('Back to contents ↑') . '</a></span>';
}

/**
 * Implements hook_form_BASE_FORM_alter().
 */
function health_adminimal_form_node_form_alter(&$form, &$form_state, $form_id) {
  array_unshift($form['actions']['submit']['#submit'],'_health_adminimal_process_date');

  // Hide option for publication collection content type.
  if ($form_id == 'publication_collection_node_form') {
    $form['field_publication_type']['#access'] = FALSE;
  }

  // Remove publication collection option from publication type list.
  if ($form_id == 'publication_node_form') {
    if (($key = array_search('Collection', $form['field_publication_type'][LANGUAGE_NONE]['#options'])) !== false) {
      unset($form['field_publication_type'][LANGUAGE_NONE]['#options'][$key]);
    }
  }
}

/**
 * Implements hook_form_alter().
 */
function health_adminimal_form_alter(&$form, &$form_state, $form_id) {

  $media_forms = array('file-entity-add-upload', 'media-internet-add-upload', 'file_entity_edit');

  if (in_array($form['#id'], $media_forms)) {

    // Make alt text mandatory.
    if (key_exists('field_file_image_alt_text', $form)) {
      $form['field_file_image_alt_text'][$form['field_file_image_alt_text']['#language']][0]['value']['#required'] = TRUE;
      $form['field_file_image_alt_text'][$form['field_file_image_alt_text']['#language']][0]['#required'] = TRUE;
      $form['field_file_image_alt_text'][$form['field_file_image_alt_text']['#language']]['#required'] = TRUE;
    }

    // Clear filename from title to force users to enter a sensible title.
    if (key_exists('filename', $form)) {
      $form['filename']['#default_value'] = '';
    }

  }

  // Add logic of if a news or event node is marked as featured, it should be validated with value in featured image field. 
  if ($form_id == 'news_article_node_form' || $form_id == 'event_node_form') {
    $form['#validate'][] = 'health_adminimal_feature_validator';
  }

  // Add logic to invalid the form if last updated date is not later than publication date.
  if ($form_id == 'publication_node_form') {
    $form['#validate'][] = '_health_adminimal_publication_date_validator';
  }

  // Assert last updated field value to be the first published date.
  if ($form_id == 'news_article_node_form') {
    $form['field_date_updated']['#access'] = FALSE;
    $form['#submit'][] = '_health_adminimal_news_update_date_submitter';
  }

  // Target audience group - disable for some content types.
  if (key_exists('#bundle', $form)) {
    $disabled = [
      'health_topic',
      'health_topic_hp',
      'condition_or_disease',
    ];
    if (in_array($form['#bundle'], $disabled)) {
      $form['field_audience']['#disabled'] = TRUE;
    }
  }
}

/**
 * Implements theme_fieldset().
 */
function health_adminimal_fieldset($variables) {
  $element = $variables['element'];

  // Make the information below a wysiwyg in a collapsible fieldset.
  if (key_exists('guidelines', $element)) {
    $element['#title'] = 'Formatting information';
    $element['#attributes']['class'][] = 'collapsible';
    $element['#attributes']['class'][] = 'collapsed';
  }

  element_set_attributes($element, array('id'));
  _form_set_class($element, array('form-wrapper'));

  $output = '<fieldset' . drupal_attributes($element['#attributes']) . '>';
  if (!empty($element['#title'])) {
    // Always wrap fieldset legends in a SPAN for CSS positioning.
    $output .= '<legend><span class="fieldset-legend">' . $element['#title'] . '</span></legend>';
  }
  $output .= '<div class="fieldset-wrapper">';
  if (!empty($element['#description'])) {
    $output .= '<div class="fieldset-description">' . $element['#description'] . '</div>';
  }
  $output .= $element['#children'];
  if (isset($element['#value'])) {
    $output .= $element['#value'];
  }
  $output .= '</div>';
  $output .= "</fieldset>\n";
  return $output;
}



/**
 * Implements theme_form_element().
 */
function health_adminimal_form_element($variables) {
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
  $attributes['class'] = array('form-item');
  if (!empty($element['#type'])) {
    $attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
  }
  if (!empty($element['#name'])) {
    $attributes['class'][] = 'form-item-' . strtr($element['#name'], array(' ' => '-', '_' => '-', '[' => '-', ']' => ''));
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
      // Help text.
      if (!empty($element['#description'])) {
        $output .= '<div class="description">' . $element['#description'] . "</div>\n";
      }
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;

    case 'after':
      $output .= ' ' . $prefix . $element['#children'] . $suffix;
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      // Help text.
      if (!empty($element['#description'])) {
        $output .= '<div class="description">' . $element['#description'] . "</div>\n";
      }
      break;

    case 'none':
    case 'attribute':
      // Help text.
      if (!empty($element['#description'])) {
        $output .= '<div class="description">' . $element['#description'] . "</div>\n";
      }
      // Output no label and no required marker, only the children.
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  $output .= "</div>\n";

  return $output;
}

/**
 * Node form validator for news/event.
 *
 * @param $form
 * @param $form_state
 */
function health_adminimal_feature_validator($form, &$form_state) {
  if (isset($form_state['values']['field_promoted_to_feature'][LANGUAGE_NONE])) {
    if ($form_state['values']['field_promoted_to_feature'][LANGUAGE_NONE][0]['value'] == 1) {
      // Check feature image value.
      $feature_iamge_id = $form_state['values']['field_feature_image'][LANGUAGE_NONE][0]['fid'];
      if ($feature_iamge_id == 0) {
        form_set_error('field_feature_image', t('Feature image field cannot be empty if marked as promoted to feature.'));
      }
    }
  }
}

/**
 * Node form validator for publication content type.
 *
 * @param $form
 * @param $form_state
 */
function _health_adminimal_publication_date_validator($form, &$form_state) {
  // Last updated field must be later than publication date.
  if (isset($form_state['values']['field_date_updated'][LANGUAGE_NONE]) && isset($form_state['values']['field_publication_date'][LANGUAGE_NONE])) {
    if (strtotime($form_state['values']['field_date_updated'][LANGUAGE_NONE][0]['value']) < strtotime($form_state['values']['field_publication_date'][LANGUAGE_NONE][0]['value'])) {
      form_set_error('field_date_updated', t('Last updated date must be later than publication date.'));
    }
  }
}

/**
 * Custom submit handler to hard code last updated value to be the first
 * published date value for news content type.
 *
 * @param $form
 * @param $form_state
 */
function _health_adminimal_news_update_date_submitter($form, &$form_state) {
  $nid = $form['nid']['#value'];
  if ($nid != NULL) {
    // When editing an existing node, which may have a first published date.
    $first_published = _health_find_first_publish_date($nid);
    if ($first_published != 'Unpublished') {
      $date = format_date(strtotime($first_published), 'custom', 'Y-m-d H:i:s');
      $form_state['values']['field_date_updated'][LANGUAGE_NONE][0]['value'] = $date;
    }
  }
}

/**
 * Custom node form submit handler to compare the last update and last review
 * date.
 */
function _health_adminimal_process_date($form, &$form_state) {
  if (isset($form_state['values']['field_last_updated'][LANGUAGE_NONE][0]) && isset($form_state['values']['field_last_reviewed'][LANGUAGE_NONE][0])) {
    if (strtotime($form_state['values']['field_last_updated'][LANGUAGE_NONE][0]['value']) > strtotime($form_state['values']['field_last_reviewed'][LANGUAGE_NONE][0]['value'])) {
      $form_state['values']['field_last_reviewed'][LANGUAGE_NONE][0] = $form_state['values']['field_last_updated'][LANGUAGE_NONE][0];
    }
  }
}
