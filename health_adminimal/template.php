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

  // Hide option for publication collection content type.
  if ($form_id == 'publication_collection_node_form') {
    $form['field_publication_type']['#disabled'] = 'disabled';
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
    $form['field_image_featured']['#disabled'] = 'disabled';
  }

  // Prevent users from being able to change the default related contact for news and departmental media releases.
  if ($form_id == 'departmental_media_node_form' || $form_id == 'news_article_node_form') {
    $form['field_related_contact']['#disabled'] = 'disabled';
  }

  // Add character limit to 300 to summary field.
  if ($form['field_summary']) {
    $form['field_summary'][LANGUAGE_NONE][0]['value']['#attributes']['maxlength'] = 300;
  }

  // Update status field on Surveys and Events.
  if (key_exists('field_status', $form)) {
    $form['field_status']['#disabled'] = 'disabled';
    array_unshift($form['actions']['submit']['#submit'], '_health_adminimal_set_status');
  }

  // FOI requests.
  if ($form_id == 'foi_request_node_form') {
    $form['field_publication_type']['#disabled'] = 'disabled';
    $form['field_audience']['#disabled'] = 'disabled';
    $form['field_related_health_topics']['#disabled'] = 'disabled';
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

  // Add the current user role into Drupal.settings.
  global $user;
  drupal_add_js(array(
    'health_adminimal' => array(
      'user' => ['roles' => $user->roles],
    ),
  ), 'setting');

  // Moderation form.
  if ($form_id == 'workbench_moderation_moderate_form') {
    // Update publish date if the user is transitioning to the published state.
    array_unshift($form['#submit'], '_health_adminimal_date_published_submitter');
  }

  // Editing a node.
  if (array_key_exists('#node_edit_form', $form)) {

    // Handle updates to dates for all nodes.
    array_unshift($form['#submit'], '_health_adminimal_date_published_submitter');
    $form['#submit'][] = '_health_adminimal_date_updated_submitter';

    // A new draft has been created via the moderation form (ie unpublish link)
    // and the user is now editing that draft.
    if (property_exists($form['#node'], 'workbench_moderation')) {
      if ($form['#node']->workbench_moderation['current']->from_state == 'published') {
        if (array_key_exists('field_enable_manual_date_editing', $form)) {
          // Reset the manual date editing flag.
          $form['field_enable_manual_date_editing'][LANGUAGE_NONE]['#default_value'][0] = 0;
        }
      }
    }
    // The user is creating a new draft.
    if (array_key_exists('workbench_moderation_state_current', $form)) {
      if ($form['workbench_moderation_state_current']['#value'] == 'published') {
        if (array_key_exists('field_enable_manual_date_editing', $form)) {
          // Reset the manual date editing flag.
          $form['field_enable_manual_date_editing'][LANGUAGE_NONE]['#default_value'][0] = 0;
        }
      }
    }
  }

  // Media manager.
  $media_manager = FALSE;
  if (isset($form['field_related_image'])) {
    $media_manager = TRUE;
  }

  if (isset($form['field_components'])) {
    foreach ($form['field_components'][LANGUAGE_NONE] as $component) {
      if (isset($component['field_related_image'])) {
        $media_manager = TRUE;
      }
    }
  }

  if ($media_manager) {
    $form['#attached']['js'][] = [
      'data' => path_to_theme() . '/js/media-manager/selector.js',
      'type' => 'file',
    ];
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

  // Change the add button label and also add an icon.
  if (isset($element['#instance']['widget']['module']) && $element['#instance']['widget']['module'] == 'paragraphs') {
    $element['add_more']['add_more']['#value'] = t('Add');
    $element['add_more']['type']['#prefix'] = '<svg aria-hidden="true" data-prefix="fas" data-icon="plus-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-plus-circle fa-w-16 fa-3x"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm144 276c0 6.6-5.4 12-12 12h-92v92c0 6.6-5.4 12-12 12h-56c-6.6 0-12-5.4-12-12v-92h-92c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h92v-92c0-6.6 5.4-12 12-12h56c6.6 0 12 5.4 12 12v92h92c6.6 0 12 5.4 12 12v56z" class=""></path></svg>';
  }

  // Change single checkbox boolean into a checkbox group so that it gets a label.
  if (isset($element['#type']) && $element['#type'] == 'checkbox') {
    $element['#type'] = 'checkboxes';
    $element['#options'] = [$element['#on_value'] => $element['#title']];
    $element['#default_value'] = [$element['#default_value']];
    if (isset($form_state['field'][$element['#field_name']])) {
      $element['#title'] = $form_state['field'][$element['#field_name']][LANGUAGE_NONE]['instance']['label'];
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

  // Workbench moderation form.
  if (array_key_exists('node', $form_state['values'])) {

    // If this is using workbench moderation.
    if (array_key_exists('state', $form_state['values'])) {

      // The node is being published.
      if ($form_state['values']['state'] == 'published') {

        // If manual date editing is not enabled, then see if we need to update
        // the date published.
        if (property_exists($form_state['values']['node'], 'field_enable_manual_date_editing')) {
          if (empty($form_state['values']['node']->field_enable_manual_date_editing)) {

            // Check if this has already been published, if not, then set date published to today.
            if (_health_adminimal_find_first_publish_date($form_state['values']['node']->nid) == FALSE) {

              // Todays date.
              $date = format_date(time(), 'custom', 'Y-m-d') . ' 00:00:00';

              // Set date published.
              // Load the current version of the node.
              $node = node_load($form_state['values']['node']->nid, $form_state['values']['node']->vid);
              $node->field_date_published[LANGUAGE_NONE][0]['value'] = $date;
              // Set revision true, otherwise it doesn't save correctly.
              $node->revision = TRUE;
              // Set the state to the same it is before moderation, so that it doesn't
              // attempt to moderate it.
              $node->state = $form_state['values']['node']->workbench_moderation['my_revision']->state;
              // Put a system log notice so its clear why we made this revision.
              $node->log = 'SYSTEM: First publish. Setting publish date.';
              // Save the node.
              node_save($node);
              // Point the form node revision to our new revision, so it moderates
              // the right version.
              $form_state['values']['node']->vid = $node->vid;
            }
          }
        }
      }
    }
  }

  // Standard node edit form.
  if (array_key_exists('nid', $form_state['values'])) {
    if (array_key_exists('workbench_moderation_state_new', $form_state['values'])) {
      if ($form_state['values']['workbench_moderation_state_new'] == 'published') {
        // If manual date editing is not enabled, then see if we need to update
        // the date published.
        if (array_key_exists('field_enable_manual_date_editing', $form_state['values'])) {
          if ($form_state['values']['field_enable_manual_date_editing'][LANGUAGE_NONE][0]['value'] == NULL) {
            // Check if this has already been published, if not, then set date published to today.
            if (_health_adminimal_find_first_publish_date($form_state['values']['nid']) == FALSE) {
              // Todays date.
              $date = format_date(time(), 'custom', 'Y-m-d') . ' 00:00:00';
              // Set date published.
              $form_state['values']['field_date_published'][LANGUAGE_NONE][0]['value'] = $date;
            }
          }
        }
      }
    }
  }
}

/**
 * Set the last updated date to today every time a node is saved.
 *
 * field_date_updated and field_enable_manual_date_editing must exist in
 * $form['values'].
 *
 * @param $form
 * @param $form_state
 */
function _health_adminimal_date_updated_submitter($form, &$form_state) {
  if ($form_state['values']['field_enable_manual_date_editing'][LANGUAGE_NONE]['0']['value'] != 1) {
    $form_state['values']['field_date_updated'][LANGUAGE_NONE][0]['value'] = format_date(time(), 'custom', 'Y-m-d H:i:s');
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
 * Set the status based on the start and end dates.
 */
function _health_adminimal_set_status($form, &$form_state) {
  if (isset($form_state['values']['field_date_start'][LANGUAGE_NONE][0]) && isset($form_state['values']['field_date_end'][LANGUAGE_NONE][0])) {
    if (strtotime('now') < strtotime($form_state['values']['field_date_start'][LANGUAGE_NONE][0]['value'])) {
      $status = "Upcoming";
    } else if (strtotime('now') < strtotime($form_state['values']['field_date_end'][LANGUAGE_NONE][0]['value'])) {
      $status = "Open";
    } else {
      $status = "Closed";
    }
    $form_state['values']['field_status'][LANGUAGE_NONE][0]['value'] = $status;
  }
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

/**
 * Adds a wrapper around a preview of a media file.
 */
function health_adminimal_media_thumbnail($variables) {
  $label = '';
  $element = $variables['element'];

  // Wrappers to go around the thumbnail.
  $attributes = array(
    'title' => $element['#name'],
    'class' => 'media-item media-type__' . $element['#file']->type,
    'data-fid' => $element['#file']->fid,
  );
  $prefix = '<div ' . drupal_attributes($attributes) . '><div class="media-thumbnail">';
  $suffix = '</div></div>';

  // Arguments for the thumbnail link.
  $thumb = '<div class="media-thumbnail__image">' . $element['#children'] . '</div>';
  if (file_entity_access('update', $element['#file'])) {
    $target = 'file/' . $element['#file']->fid . '/edit';
    $title = t('Click to edit details');
  }
  else {
    $target = 'file/' . $element['#file']->fid;
    $title = t('Click to view details');
  }
  $options = array(
    'query' => drupal_get_destination(),
    'html' => TRUE,
    'attributes' => array('title' => $title),
  );

  // Element should be a field renderable array. This should be improved.
  if (!empty($element['#show_names']) && $element['#name']) {
    $label = '<div class="label-wrapper"><label class="media-filename">' . $element['#name'] . '</label></div>';
  }

  $output = $prefix;
  if (!empty($element['#add_link'])) {
    $output .= l($thumb, $target, $options);
  }
  else {
    $output .= $thumb;
  }
  $output .= $label . $suffix;
  return $output;
}

function health_adminimal_file_icon($variables) {
  $mime = check_plain($variables['file']->filemime);
  switch($mime) {
    case 'application/pdf':
      return '<svg data-mime="'.$mime.'" aria-hidden="true" data-prefix="far" data-icon="file-pdf" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="svg-inline--fa fa-file-pdf fa-w-12 fa-5x"><path fill="currentColor" d="M369.9 97.9L286 14C277 5 264.8-.1 252.1-.1H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V131.9c0-12.7-5.1-25-14.1-34zM332.1 128H256V51.9l76.1 76.1zM48 464V48h160v104c0 13.3 10.7 24 24 24h104v288H48zm250.2-143.7c-12.2-12-47-8.7-64.4-6.5-17.2-10.5-28.7-25-36.8-46.3 3.9-16.1 10.1-40.6 5.4-56-4.2-26.2-37.8-23.6-42.6-5.9-4.4 16.1-.4 38.5 7 67.1-10 23.9-24.9 56-35.4 74.4-20 10.3-47 26.2-51 46.2-3.3 15.8 26 55.2 76.1-31.2 22.4-7.4 46.8-16.5 68.4-20.1 18.9 10.2 41 17 55.8 17 25.5 0 28-28.2 17.5-38.7zm-198.1 77.8c5.1-13.7 24.5-29.5 30.4-35-19 30.3-30.4 35.7-30.4 35zm81.6-190.6c7.4 0 6.7 32.1 1.8 40.8-4.4-13.9-4.3-40.8-1.8-40.8zm-24.4 136.6c9.7-16.9 18-37 24.7-54.7 8.3 15.1 18.9 27.2 30.1 35.5-20.8 4.3-38.9 13.1-54.8 19.2zm131.6-5s-5 6-37.3-7.8c35.1-2.6 40.9 5.4 37.3 7.8z" class=""></path></svg>';
    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
    case 'application/msword':
      return '<svg data-mime="'.$mime.'" aria-hidden="true" data-prefix="far" data-icon="file-word" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="svg-inline--fa fa-file-word fa-w-12 fa-3x"><path fill="currentColor" d="M369.9 97.9L286 14C277 5 264.8-.1 252.1-.1H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V131.9c0-12.7-5.1-25-14.1-34zM332.1 128H256V51.9l76.1 76.1zM48 464V48h160v104c0 13.3 10.7 24 24 24h104v288H48zm220.1-208c-5.7 0-10.6 4-11.7 9.5-20.6 97.7-20.4 95.4-21 103.5-.2-1.2-.4-2.6-.7-4.3-.8-5.1.3.2-23.6-99.5-1.3-5.4-6.1-9.2-11.7-9.2h-13.3c-5.5 0-10.3 3.8-11.7 9.1-24.4 99-24 96.2-24.8 103.7-.1-1.1-.2-2.5-.5-4.2-.7-5.2-14.1-73.3-19.1-99-1.1-5.6-6-9.7-11.8-9.7h-16.8c-7.8 0-13.5 7.3-11.7 14.8 8 32.6 26.7 109.5 33.2 136 1.3 5.4 6.1 9.1 11.7 9.1h25.2c5.5 0 10.3-3.7 11.6-9.1l17.9-71.4c1.5-6.2 2.5-12 3-17.3l2.9 17.3c.1.4 12.6 50.5 17.9 71.4 1.3 5.3 6.1 9.1 11.6 9.1h24.7c5.5 0 10.3-3.7 11.6-9.1 20.8-81.9 30.2-119 34.5-136 1.9-7.6-3.8-14.9-11.6-14.9h-15.8z" class=""></path></svg>';
    case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
      return '<svg data-mime="'.$mime.'" aria-hidden="true" data-prefix="far" data-icon="file-excel" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="svg-inline--fa fa-file-excel fa-w-12 fa-3x"><path fill="currentColor" d="M369.9 97.9L286 14C277 5 264.8-.1 252.1-.1H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V131.9c0-12.7-5.1-25-14.1-34zM332.1 128H256V51.9l76.1 76.1zM48 464V48h160v104c0 13.3 10.7 24 24 24h104v288H48zm212-240h-28.8c-4.4 0-8.4 2.4-10.5 6.3-18 33.1-22.2 42.4-28.6 57.7-13.9-29.1-6.9-17.3-28.6-57.7-2.1-3.9-6.2-6.3-10.6-6.3H124c-9.3 0-15 10-10.4 18l46.3 78-46.3 78c-4.7 8 1.1 18 10.4 18h28.9c4.4 0 8.4-2.4 10.5-6.3 21.7-40 23-45 28.6-57.7 14.9 30.2 5.9 15.9 28.6 57.7 2.1 3.9 6.2 6.3 10.6 6.3H260c9.3 0 15-10 10.4-18L224 320c.7-1.1 30.3-50.5 46.3-78 4.7-8-1.1-18-10.3-18z" class=""></path></svg>';
    case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
      return '<svg data-mime="'.$mime.'" aria-hidden="true" data-prefix="far" data-icon="file-powerpoint" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="svg-inline--fa fa-file-powerpoint fa-w-12 fa-3x"><path fill="currentColor" d="M369.9 97.9L286 14C277 5 264.8-.1 252.1-.1H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V131.9c0-12.7-5.1-25-14.1-34zM332.1 128H256V51.9l76.1 76.1zM48 464V48h160v104c0 13.3 10.7 24 24 24h104v288H48zm72-60V236c0-6.6 5.4-12 12-12h69.2c36.7 0 62.8 27 62.8 66.3 0 74.3-68.7 66.5-95.5 66.5V404c0 6.6-5.4 12-12 12H132c-6.6 0-12-5.4-12-12zm48.5-87.4h23c7.9 0 13.9-2.4 18.1-7.2 8.5-9.8 8.4-28.5.1-37.8-4.1-4.6-9.9-7-17.4-7h-23.9v52z" class=""></path></svg>';
    default:
      return '<svg data-mime="'.$mime.'" aria-hidden="true" data-prefix="far" data-icon="file-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="svg-inline--fa fa-file-alt fa-w-12 fa-5x"><path fill="currentColor" d="M288 248v28c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-28c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm-12 72H108c-6.6 0-12 5.4-12 12v28c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-28c0-6.6-5.4-12-12-12zm108-188.1V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V48C0 21.5 21.5 0 48 0h204.1C264.8 0 277 5.1 286 14.1L369.9 98c9 8.9 14.1 21.2 14.1 33.9zm-128-80V128h76.1L256 51.9zM336 464V176H232c-13.3 0-24-10.7-24-24V48H48v416h288z" class=""></path></svg>';
  }
}

/**
 * Implements hook_node_access_alter().
 *
 * Stolen from https://www.drupal.org/project/menu_view_unpublished.
 *
 * @param \QueryAlterableInterface $query
 */
function health_adminimal_query_node_access_alter(QueryAlterableInterface $query) {
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
