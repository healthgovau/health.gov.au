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
  $output .= '<nav class="index-links rs_preserve rs_skip">';
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

    // Toggle publication NMM text field based on the value in publication
    // orderable field.
    $form['field_publication_nmm_id']['#states'] = [
      'visible' => [
        ':input[id="edit-field-publication-orderable-und"]' => ['checked' => TRUE],
      ],
    ];

    $form['#validate'][] = '_health_adminimal_publication_validate';
  }

  // Prevent users from being able to change the default image for media releases.
  if ($form_id == 'departmental_media_node_form') {
    $form['field_image_featured']['#access'] = FALSE;
  }

  // Prevent users from being able to change the default related contact for news and departmental media releases.
  if ($form_id == 'departmental_media_node_form' || $form_id == 'news_article_node_form') {
    $form['field_related_contact']['#access'] = FALSE;
  }

  // Add character limit to 200 to summary field.
  if ($form['field_summary']) {
    $form['field_summary'][LANGUAGE_NONE][0]['value']['#attributes']['maxlength'] = 200;
  }
}

/**
 * Implements hook_form_alter().
 */
function health_adminimal_form_alter(&$form, &$form_state, $form_id) {

  $add_media_forms = ['file_entity_add_upload', 'media_internet_add_upload'];
  $edit_media_forms = ['file_entity_edit'];

  if (in_array($form['#form_id'], $add_media_forms)) {
    // Clear filename from title to force users to enter a sensible title.
    if (key_exists('filename', $form)) {
      $form['filename']['#default_value'] = '';
      $form['#submit'][] = '_health_adminimal_file_rename_submitter';
    }
  }
  if (in_array($form['#form_id'], $edit_media_forms)) {
    $form['actions']['submit']['#submit'][] = '_health_adminimal_file_rename_submitter';
  }

  // Add logic of if a news or event node is marked as featured, it should be validated with value in featured image field.
  if ($form_id == 'news_article_node_form' || $form_id == 'event_node_form') {
    $form['#validate'][] = 'health_adminimal_feature_validator';
  }

  // Update date published if the user is changing moderation states.
  if ($form_id == 'workbench_moderation_moderate_form') {
    $form['#submit'][] = '_health_adminimal_date_published_submitter';
  }

  // Handle updates to dates for all nodes.
  if (key_exists('#node', $form)) {
    if (!theme_get_setting('manually_edit_dates')) {
      $form['field_date_published']['#access'] = FALSE;
      $form['field_date_updated']['#access'] = FALSE;
    }
    $form['#submit'][] = '_health_adminimal_date_updated_submitter';
    $form['#submit'][] = '_health_adminimal_date_published_submitter';
  }

  // Target audience group - disable for some content types.
  if (key_exists('#bundle', $form)) {
    $disabled = [
      'health_topic',
      'health_topic_hp',
      'condition_or_disease',
      'committee_or_group'
    ];
    if (in_array($form['#bundle'], $disabled)) {
      $form['field_audience']['#disabled'] = TRUE;
    }
  }

  // Allow more than 128 characters for view descriptions.
  if ($form['#form_id'] == 'views_ui_edit_display_form') {
    $form['options']['display_description']['#maxlength'] = 512;
  }

  // Video duration validation
  if (isset($form['field_resource_duration'])) {
    $form['field_resource_duration']['#element_validate'][] = '_health_adminimal_resource_duration_validator';
  }

  // Email address validation.
  if (isset($form['field_contact_email'])) {
    $form['field_contact_email']['#element_validate'][] = '_health_adminimal_email_validator';
  }

  // Telephone number validation.
  if (isset($form['field_contact_telephone'])) {
    $form['field_contact_telephone']['#element_validate'][] = '_health_adminimal_telephone_validator';
  }

  // Fax number validation.
  if (isset($form['field_contact_fax_number'])) {
    $form['field_contact_fax_number']['#element_validate'][] = '_health_adminimal_telephone_validator';
  }

  // Content owner. Create groups.
  if (isset($form['field_content_owner'])) {
    $form['field_content_owner']['und']['#options'] = _health_adminimal_optgroup('content_owner');
  }
}

/**
 * Create a nested array of a taxonomy suitable for a select.
 *
 * @param $machine_name
 *    Taxonomy machine name
 *
 * @return array|bool
 */
function _health_adminimal_optgroup($machine_name) {
  $vocabulary = taxonomy_vocabulary_machine_name_load($machine_name);
  $terms = entity_load('taxonomy_term', FALSE, array('vid' => $vocabulary->vid));
  $new_options = array();
  foreach ($terms as $options_key => $options_value) {
    if (is_numeric($options_key)) {
      $parents = taxonomy_get_parents($options_key);
      if (count($parents) == 0) {
        $new_options = _health_adminimal_optgroup_children($options_value);
      }
    }
  }
  return $new_options;
}

/**
 * Return an array of children suitable for a select.
 *
 * @param $term
 *   Taxonomy term object.
 *
 * @return array|bool
 */
function _health_adminimal_optgroup_children($term) {
  $new_options = [];
  $children = taxonomy_get_children($term->tid);
  if (count($children)) {
    foreach ($children as $child_term) {
      $child_options = _health_adminimal_optgroup_children($child_term);
      if ($child_options === FALSE) {
        $new_options[$child_term->tid] = $child_term->name;
      } else {
        $new_options[$child_term->name] = $child_options;
      }
    }
  } else {
    return FALSE;
  }
  return $new_options;
}

/**
 * Submit handler to rename the file to what the user has entered for the file title.
 *
 * @param $form
 * @param $form_state
 */
function _health_adminimal_file_rename_submitter($form, &$form_state) {

  $new_name = _health_adminimal_prepare_filename($form_state['values']['filename']);
  $old_name = _health_adminimal_prepare_filename($form['filename']['#default_value']);

  // First check if the filename actually needs renaming.
  if ($new_name != $old_name || !empty($form_state['values']['replace_upload'])) {
    // Get the file.
    $file = $form_state['file'];
    // Get the file extension.
    $path_info = pathinfo($file->uri);
    $new_name .= '.' . $path_info['extension'];
    // Generate the filename, this checks if the file already exists and adds to it.
    $new_name = file_create_filename($new_name, file_uri_scheme($file->uri) . '://');
    // Rename the file (move it).
    file_move($file, $new_name);
    // Moving sets the actual file name as the title, so revert that back to what it should be.
    $file = file_load($file->fid);
    $file->filename = $form_state['values']['filename'];
    file_save($file);
  }
}

function _health_adminimal_prepare_filename($name) {
  // Replace anything not normal with a hyphen.
  $name = strtolower(preg_replace('/[^a-zA-Z\d]+/', '-', $name));
  // Remove any hyphens at the start.
  $name = preg_replace('/^-/', '', $name);
  // Remove any hyphens at the end.
  $name = preg_replace('/-$/', '', $name);
  return $name;
}

/**
 * Implements hook_field_widget_form_alter().
 *
 * @param $element
 * @param $form_state
 * @param $context
 */
function health_adminimal_field_widget_form_alter(&$element, &$form_state, $context) {
  // Add email validator to email paragraph.
  if (isset($element['#field_name']) && $element['#field_name'] == 'field_contact_email') {
    $element['#element_validate'][] = '_health_adminimal_email_validator';
  }

  // Add telephone validator to telephone paragraph.
  if (isset($element['#field_name']) && $element['#field_name'] == 'field_contact_telephone') {
    $element['#element_validate'][] = '_health_adminimal_telephone_validator';
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
      // Add description back in if this is a text area.
      if ($element['#type'] == 'textarea') {
        if (key_exists('#entity_type', $element)) {
          $info = field_info_instance($element['#entity_type'], $element['#field_name'], $element['#bundle']);
          $element['#description'] = $info['description'];
        }
      }
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
 * Implements theme_text_format_wrapper
 */
function health_adminimal_text_format_wrapper($variables) {
  $element = $variables['element'];
  $output = '<div class="text-format-wrapper">';
  $output .= $element['#children'];
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
 * Video duration validator.
 *
 * @param $form
 * @param $form_state
 */
function _health_adminimal_resource_duration_validator($element, &$form_state) {
  $field_name = $element[LANGUAGE_NONE]['#field_name'];
  $value = $form_state['values'][$field_name][LANGUAGE_NONE][0]['value'];
  if (!empty($value) && preg_match('/^([1-9])?[0-9]:([1-5]?[0-9]:)?[0-5][0-9]/', $value) == 0) {
    form_error($element, t('Duration must be in the format HH:MM:SS with no leading spaces, for example <strong>2:26</strong> (2 minutes and 26 seconds) or <strong>1:43:59</strong> (one hour, 43 minutes and 59 seconds).'));
  }
}

/**
 * Email address validator.
 *
 * @param $form
 * @param $form_state
 */
function _health_adminimal_email_validator($element, &$form_state) {
  if ($element['#entity_type'] == 'paragraphs_item') {
    $value = $element['value']['#value'];
  } else {
    $field_name = $element[LANGUAGE_NONE]['#field_name'];
    $value = $form_state['values'][$field_name][LANGUAGE_NONE][0]['value'];
  }
  if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
    form_error($element, t('Email address is not in a valid format.'));
  }
}


/**
 * Telephone number validator.
 *
 * @param $form
 * @param $form_state
 */
function _health_adminimal_telephone_validator($element, &$form_state) {
  if (isset($element['#entity_type']) && $element['#entity_type'] == 'paragraphs_item') {
    $value = $element['value']['#value'];
  } else {
    $field_name = $element[LANGUAGE_NONE]['#field_name'];
    $value = $form_state['values'][$field_name][LANGUAGE_NONE][0]['value'];
  }
  if (!empty($value) && (preg_match('/^(\d{2}|\d{4}) ?(\d{4}|\d{3}) ?(\d{4}|\d{3})$/', $value) || preg_match('/^\d{3} \d{3}$/', $value)) == 0) {
    form_error($element, t('Telephone number is not in a valid format, eg:<br/>Local: 02 1234 5678 <br/>Mobile: 0412 345 678 <br/>Hotline: 1300 123 456 <br/>Hotline: 130 000'));
  }
}

/**
 * Update date published to the current day if this is the first time it is being published.
 *
 * @param $form
 * @param $form_state
 */
function _health_adminimal_date_published_submitter($form, &$form_state) {
  // If date editing is enabled, don't automatically update.
  if (theme_get_setting('manually_edit_dates')) {
    return;
  }

  // Standard node edit form.
  if (key_exists('nid', $form_state['values'])) {

    // Check if date published is empty - this should only happen on nodes that existed before date published field was added.
    if (empty($form_state['values']['field_date_published'][LANGUAGE_NONE][0])) {
      // If it has already been published, fill in the date.
      if ($first_published = _health_adminimal_find_first_publish_date($form_state['values']['nid'])) {
        $date = format_date(strtotime($first_published), 'custom', 'Y-m-d H:i:s');
        $form_state['values']['field_date_published'][LANGUAGE_NONE][0]['value'] = $date;
      }
    }

    // If this is being published from an unpublished state.
    if (key_exists('workbench_moderation_state_new', $form_state['values'])) { // If this is using workbench moderation
      if ($form_state['values']['workbench_moderation_state_new'] == 'published' && $form_state['values']['status'] == 0) {
        // Check if this has already been published, if not, then set date published to today.
        if (_health_adminimal_find_first_publish_date($form_state['values']['nid']) == FALSE) {
          $date = format_date(time(), 'custom', 'Y-m-d') . ' 00:00:00';
          $form_state['values']['field_date_published'][LANGUAGE_NONE][0]['value'] = $date;
        }
      }
    }
  }

  // Workbench moderation state change.
  if ($form['#id'] == 'workbench-moderation-moderate-form') {

    // If this is the being published from an unpublished state.
    if ($form_state['values']['state'] == 'published' && $form_state['values']['node']->status == 0) {
      $node = node_load($form_state['values']['node']->nid);
      // Set the date published to when it was first published.
      if ($first_published = _health_adminimal_find_first_publish_date($node->nid)) {
        $date = format_date(strtotime($first_published), 'custom', 'Y-m-d H:i:s');
        $node->field_date_published[LANGUAGE_NONE][0]['value'] = $date;
        node_save($node);
      }

    }
  }
}

/**
 * Set the last updated date to today every time a node is saved.
 *
 * @param $form
 * @param $form_state
 */
function _health_adminimal_date_updated_submitter($form, &$form_state) {
  // If date editing is enabled, don't automatically update.
  if (theme_get_setting('manually_edit_dates')) {
    return;
  }
  $form_state['values']['field_date_updated'][LANGUAGE_NONE][0]['value'] = format_date(time(), 'custom', 'Y-m-d H:i:s');
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


/**
 * Find the first publish date by given node ID.
 *
 * @param $nid
 *   Node ID.
 *
 * @return mixed|string
 */
function _health_adminimal_find_first_publish_date($nid) {
  // Find the fist published date for current node.
  // Not a good way to implement, no API from workbench_moderation module
  // available.
  $result = db_query('
    SELECT m.stamp
    FROM {node_revision} r
    LEFT JOIN {node} n ON n.vid = r.vid
    INNER JOIN {workbench_moderation_node_history} m ON m.vid = r.vid
    WHERE r.nid = :nid
    AND m.state = :published_state
    ORDER BY m.stamp ASC
    LIMIT 1',
    [
      ':nid' => $nid,
      ':published_state' => workbench_moderation_state_published(),
    ])
    ->fetchObject();
  if (empty($result->stamp)) {
    $date = FALSE;
  }
  else {
    $date = format_date($result->stamp, 'custom', 'd F Y');
  }

  return $date;
}

/**
 * Custom form validator for publication node form.
 *
 * Make publication nmm id field required if publication orderable field is
 * checked.
 *
 * Remove publication nmm id value if publication orderable field is unchecked.
 *
 * @param $form
 * @param $form_state
 */
function _health_adminimal_publication_validate($form, &$form_state) {
  if ($form_state['values']['field_publication_orderable'][LANGUAGE_NONE][0]['value'] == TRUE) {
    if ($form_state['values']['field_publication_nmm_id'][LANGUAGE_NONE][0]['value'] == NULL) {
      form_set_error('field_publication_nmm_id', 'Please fill in publication nmm id');
    }
  }
  else {
    if ($form_state['values']['field_publication_nmm_id'][LANGUAGE_NONE][0]['value'] != NULL) {
      $form_state['values']['field_publication_nmm_id'][LANGUAGE_NONE][0]['value'] = NULL;
    }
  }
}

/**
 * Implements hook_js_alter().
 *
 * Perform necessary alterations to the JavaScript before it is presented on the page.
 *
 * @param array $javascript
 *   An array of all JavaScript being presented on the page.
 */
function health_adminimal_js_alter(&$javascript) {
  // Add/replace chosen js.
  $javascript['profiles/govcms/libraries/chosen/chosen.jquery.min.js'] = [
    'data' => drupal_get_path('theme', 'health_adminimal') . '/js/libraries/chosen/chosen.jquery.min.js',
    'version' => '1',
    'group' => -100,
    'type' => 'file',
    'weight' => 1,
    'every_page' => FALSE,
    'preprocess' => TRUE,
    'requires_jquery' => TRUE,
    'scope' => 'header',
    'cache' => TRUE,
    'defer' => FALSE,
  ];
  // Remove standard chosen config as we will use our own.
  if (key_exists('profiles/govcms/modules/contrib/chosen/chosen.js', $javascript)) {
    unset($javascript['profiles/govcms/modules/contrib/chosen/chosen.js']);
  }
}

/**
 * Implements hook_css_alter().
 */
function health_adminimal_css_alter(&$css) {
  // Add/replace chosen css.
  $css['profiles/govcms/libraries/chosen/chosen.css'] = [
    'data' => drupal_get_path('theme', 'health_adminimal') . '/js/libraries/chosen/chosen.min.css',
    'group' => -100,
    'type' => 'file',
    'weight' => 1,
    'every_page' => FALSE,
    'media' => 'all',
    'preprocess' => TRUE,
    'browsers' => ['IE'=> TRUE, '!IE' => TRUE],
  ];
}