<?php
/**
 * @file
 *   All the helper functions that the template is using.
 */
/**
 * Convert a mimetype into a human readable format.
 *
 * @param string $mimetype
 *
 * @return string $human
 */
function health_get_friendly_mime($mimetype) {
  $descriptions = [
    'application/pdf' => '<abbr title="Portable Document Format">PDF</abbr>',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '<abbr title="Microsoft Word document">Word</abbr>',
    'application/msword' => '<abbr title="Microsoft Word document">Word</abbr>',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '<abbr title="Microsoft Excel document">Excel</abbr>',
    'text/plain' => 'plain text',
    'image/jpeg' => '<abbr title="Joint Photographic Experts Group">JPEG</abbr>',
    'image/png' => '<abbr title="Portable Network Graphics">PNG</abbr>',
    'image/gif' => '<abbr title="Graphics Interchange Format">GIF</abbr>',
  ];
  if (array_key_exists($mimetype, $descriptions)) {
    return $descriptions[$mimetype];
  }

  return $mimetype;
}


/**
 * Helper function to add maxlength validation to form elements.
 *
 * @param array $form
 *   From array.
 *
 * @param array $element_names
 *   Form elements with ID names.
 */
function _health_add_maxlength(&$form, $element_names) {

  $js = '';

  foreach ($element_names as $key => $length) {
    $key_id =  $key.'_count_wrapper';

    $prefix = '<div id="'.$key_id.'" class="form-element-with-counter">';
    $suffix = '<p class="form-element-length-counter">{{' . $length . ' - message.length}} characters remaining</p></div>';

    // Webform.
    if (key_exists('submitted', $form)) {
      $form['submitted'][$key]['#prefix'] = $prefix;
      $form['submitted'][$key]['#suffix'] = $suffix;
      $form['submitted'][$key]['#attributes']['v-model'] = 'message';
      $form['submitted'][$key]['#attributes']['maxlength'] = $length;
    } else { // Node form.
      $form[$key]['#prefix'] = $prefix;
      $form[$key]['#suffix'] = $suffix;
      $form[$key][$form['#node']->language][0]['value']['#attributes']['v-model'] = 'message';
      $form[$key][$form['#node']->language][0]['value']['#attributes']['maxlength'] = $length;
      $form[$key]['#element_validate'][] = 'amr_validate_maxlength';
    }
    // Vue js connector.
    $js .= "Drupal.health.fieldCounter('#" . $key_id . "');";
  }

  // Attach the field counter js.
  drupal_add_js(drupal_get_path('theme', 'health') .'/js/health.fieldcounter.js', 'file');

  // Add in the fields that we want to have this functionality.
  $form['#attached']['js']['(function ($) {$(document).ready(function(){'.$js.'});})(jQuery);'] = array(
    'type' => 'inline',
    'scope' => 'footer',
    'weight' => 0,
  );
}

/**
 * Returns HTML for a marker for new or updated content.
 */
function health_mark($variables) {
  $type = $variables['type'];

  if ($type == MARK_NEW) {
    return ' <mark>'.t('new').'</mark>';
  } elseif ($type == MARK_UPDATED) {
    return ' <mark>'.t('updated').'</mark>';
  }
}

/**
 * Helper function to find the filtered criteria name for faceted search.
 *
 * @param string $param
 *   Filter information in format "field: value".
 *
 * @return string $name
 *   Filter name.
 */
function _health_find_facet_filter_name($param) {
  $fields = explode(':', $param);
  $field_name = $fields[0];
  $value = $fields[1];

  $field_info = field_info_field($field_name);
  if ($field_info['type'] == 'taxonomy_term_reference') {
    $term = taxonomy_term_load($value);
    $name = $term->name;

    return $name;
  }
  elseif ($field_info['type'] == 'entityreference') {
    $node = node_load($value);
    $name = $node->title;

    return $name;
  }

  return $value;
}

/**
 * Find the first publish date by given node ID.
 *
 * @param $nid
 *   Node ID.
 *
 * @return mixed|string
 */
function _health_find_first_publish_date($nid) {
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
    $date = "Unpublished";
  }
  else {
    $date = format_date($result->stamp, 'custom', 'd F Y');
  }

  return $date;
}

/**
 * Helper function to generate public hp switcher render array for public topic
 * page.
 *
 * @param $variables
 * @param $full
 * @return array
 */
function _health_get_render_elements_for_public_topic_page($variables, $full) {
  if ($full == TRUE) {
    $title = 'Health professionals';
    $text = $variables['bean']->field_bean_body[LANGUAGE_NONE][0]['value'];

    // Get the current path and work back to the topic element.
    $path = drupal_get_path_alias();
    $parts = explode('/',$path);
    $url = '/topics/' . $parts[1] . '/health-professionals';
    $link = l('Find out more', $url);
  }
  else {
    $title = '';
    $text = '';

    // Get the current path and work back to the topic element.
    $path = drupal_get_path_alias();
    $parts = explode('/',$path);
    $url = '/topics/' . $parts[1] . '/health-professionals';
    $link = l('For health professionals', $url);
  }

  return [
    'title' => $title,
    'text' => $text,
    'link' => $link,
  ];
}

/**
 * Helper function to generate public hp switcher render array for health
 * professionals topic page.
 *
 * @param $variables
 * @param $full
 * @return array
 */
function _health_get_render_elements_for_hp_topic_page($variables, $full) {
  if ($full == TRUE) {
    $title = 'General public';
    $text = $variables['bean']->field_bean_text[LANGUAGE_NONE][0]['value'];

    // Get the current path and work back to the topic element.
    $path = drupal_get_path_alias();
    $parts = explode('/',$path);
    $url = '/topics/' . $parts[1];
    $link = l('Find out more', $url);
  }
  else {
    $title = '';
    $text = '';

    // Get the current path and work back to the topic element.
    $path = drupal_get_path_alias();
    $parts = explode('/',$path);
    $url = '/topics/' . $parts[1];
    $link = l('For general public', $url);
  }

  return [
    'title' => $title,
    'text' => $text,
    'link' => $link,
  ];
}

/**
 * Return the correct classes and parameters for labels on a preprocess ds field.
 *
 * @param $variables
 * @param $field
 * @param $field_variables
 *
 * @return mixed
 */
function _health_label_display($variables, $field, $field_variables) {
  // Get the label according to the display mode.
  $entity_fields = ds_get_fields('node');
  $field_variables['label'] = $entity_fields[$field]['title'];

  // Get the display of the label according to the display mode.
  $field_settings = ds_get_field_settings('node', $variables['type'], $variables['view_mode']);
  if (isset($field_settings[$field])) {
    switch ($field_settings[$field]['label']) {
      case 'hidden':
        $field_variables['label_hidden'] = TRUE;
        break;
      case 'above':
        $field_variables['label_hidden'] = FALSE;
        $field_variables['classes'] = 'field-label-above';
        break;
      case 'inline':
        $field_variables['label_hidden'] = FALSE;
        $field_variables['classes'] = 'field-label-inline';
        break;
    }
  }
  return $field_variables;
}

/**
 * Check if a path should have a default audience filter applied and return
 * appropriate query string.
 *
 * @param $path
 *
 * @return array|bool
 *   Query array used in link options.
 */
function _health_default_audience_menu($path) {
  $audience_default_paths = array(
    'news-and-events',
    'resources',
    'initiatives-and-programs',
  );
  foreach ($audience_default_paths as $audience_default_path) {
    if ($path == $audience_default_path) {
      return ['f' => [0 => 'field_audience:451']];
    }
  }
  return FALSE;
}