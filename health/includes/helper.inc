<?php

/**
 * @file
 * All the helper functions that the template is using.
 */

/**
 * Convert a mimetype into a human readable format.
 *
 * @param string $mimetype
 *   Mime type.
 *
 * @return string
 *   Human readable mime type.
 */
function health_get_friendly_mime($mimetype) {
  $descriptions = [
    'application/pdf' => '<abbr title="Portable Document Format">PDF</abbr>',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '<abbr title="Microsoft Word document">Word</abbr>',
    'application/msword' => '<abbr title="Microsoft Word document">Word</abbr>',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '<abbr title="Microsoft Excel document">Excel</abbr>',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation' => '<abbr title="Microsoft PowerPoint document">PowerPoint</abbr>',
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
 * @param array $element_names
 *   Form elements with ID names.
 */
function _health_add_maxlength(array &$form, array $element_names) {

  $js = '';

  foreach ($element_names as $key => $length) {
    $key_id = $key . '_count_wrapper';

    $prefix = '<div id="' . $key_id . '" class="form-element-with-counter">';
    $suffix = '<p class="form-element-length-counter text--minor">' . $length . ' characters remaining</p></div>';

    // Webform.
    if (array_key_exists('submitted', $form)) {
      $form['submitted'][$key]['#prefix'] = $prefix;
      $form['submitted'][$key]['#suffix'] = $suffix;
      $form['submitted'][$key]['#attributes']['maxlength'] = $length;
    }
    // Node form.
    else {
      $form[$key]['#prefix'] = $prefix;
      $form[$key]['#suffix'] = $suffix;
      $form[$key][$form['#node']->language][0]['value']['#attributes']['maxlength'] = $length;
      $form[$key]['#element_validate'][] = 'amr_validate_maxlength';
    }
    $js .= 'Drupal.health.fieldCounter("#' . $key_id . '", ' . $length . ');';
  }

  // Attach the field counter js.
  $form['#attached']['js'][] = drupal_get_path('theme', 'health') . '/js/dist/health.fieldcounter.min.js';

  // Add in the fields that we want to have this functionality.
  $form['#attached']['js']['(function ($) {$(document).ready(function(){' . $js . '});})(jQuery);'] = [
    'type' => 'inline',
    'scope' => 'footer',
    'weight' => 0,
  ];
}

/**
 * Returns HTML for a marker for new or updated content.
 *
 * @param array $variables
 *   Who knows.
 *
 * @return string
 *   Who knows.
 */
function health_mark(array $variables) {
  $type = $variables['type'];
  if ($type == MARK_NEW) {
    return ' <mark>' . t('new') . '</mark>';
  }
  elseif ($type == MARK_UPDATED) {
    return ' <mark>' . t('updated') . '</mark>';
  }
  return '';
}

/**
 * Helper function to find the filtered criteria name for faceted search.
 *
 * @param string $param
 *   Filter information in format "field: value".
 *
 * @return string
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
 * Generates public hp switcher render array for public topic page.
 *
 * @param array $variables
 *   Preprocess variables.
 *
 * @return array
 *   Render array.
 */
function _health_get_render_elements_for_public_topic_page(array $variables) {
  $title = $variables['bean']->field_pbundle_title[LANGUAGE_NONE][0]['value'];
  $text = $variables['bean']->field_bean_body[LANGUAGE_NONE][0]['value'];

  // Get the current path and work back to the topic element.
  $path = drupal_get_path_alias();
  $parts = explode('/', $path);
  $url = '/health-topics/' . $parts[1] . '/health-professionals';

  return [
    'title' => $title,
    'text' => $text,
    'url' => $url,
  ];
}

/**
 * Generate public hp switcher render array for health professionals topic page.
 *
 * @param array $variables
 *   Preprocess variables.
 *
 * @return array
 *   Render array.
 */
function _health_get_render_elements_for_hp_topic_page(array $variables) {
  $title = $variables['bean']->field_title[LANGUAGE_NONE][0]['value'];
  $text = $variables['bean']->field_bean_text[LANGUAGE_NONE][0]['value'];

  // Get the current path and work back to the topic element.
  $path = drupal_get_path_alias();
  $parts = explode('/', $path);
  $url = '/health-topics/' . $parts[1];

  return [
    'title' => $title,
    'text' => $text,
    'url' => $url,
    'font' => 'fa-medkit',
  ];
}

/**
 * Get the correct classes and parameters for labels on a preprocess ds field.
 *
 * @param array $variables
 *   Preprocess variables.
 * @param string $field
 *   The field machine name.
 *
 * @return array
 *   Field settings.
 */
function _health_label_display(array $variables, $field) {
  // Set some defaults needed to render.
  $field_variables = [
    'title_attributes' => '',
    'content_attributes' => '',
    'item_attributes' => [0 => ''],
    'attributes' => '',
  ];

  // Get the label according to the display mode.
  $entity_fields = ds_get_fields('node');
  $field_variables['label'] = $entity_fields[$field]['title'];

  // Add some default classes that are needed.
  $field_variables['classes'] = 'field clearfix field-name-ds-' . str_replace('_', '-', $field);

  // Get the display of the label according to the display mode.
  $field_settings = ds_get_field_settings('node', $variables['type'], $variables['view_mode']);
  if (isset($field_settings[$field])) {
    switch ($field_settings[$field]['label']) {
      case 'hidden':
        $field_variables['label_hidden'] = TRUE;
        break;

      case 'above':
        $field_variables['label_hidden'] = FALSE;
        $field_variables['classes'] .= ' field-label-above';
        break;

      case 'inline':
        $field_variables['label_hidden'] = FALSE;
        $field_variables['classes'] .= ' field--inline';
        break;
    }
  }

  $field_variables['settings'] = $field_settings;
  return $field_variables;
}

/**
 * Get the weight of a Display Suite field using the current view mode.
 *
 * @param array $variables
 *   Preprocess variables.
 * @param string $field
 *   The field to get the weight for.
 *
 * @return int
 *   The weight
 */
function _health_get_ds_field_weight(array $variables, $field) {
  $field_settings = ds_get_field_settings('node', $variables['type'], $variables['view_mode']);
  return $field_settings[$field]['weight'];
}

/**
 * Check if a path should have the default audience filter applied.
 *
 * @param string $path
 *   URL path.
 *
 * @return array|bool
 *   Query array used in link options.
 */
function _health_default_audience_menu($path) {
  $audience_default_paths = [
    'news-and-events',
    'resources',
    'initiatives-and-programs',
    'services',
  ];
  foreach ($audience_default_paths as $audience_default_path) {
    if (substr($path, 0, strlen($audience_default_path)) === $audience_default_path) {
      return ['f' => [0 => 'field_audience:451']];
    }
  }
  return FALSE;
}

/**
 * Generate a random letter based ID of varying lengths.
 *
 * @param int $length
 *   The length of the id.
 *
 * @return string
 *   The id.
 */
function _health_gen_uid($length = 10) {
  $str = "";
  for ($x = 0; $x < $length; $x++) {
    $str .= substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1);
  }
  return $str;
}

/**
 * Find the featured item based on the paragraph bundle.
 *
 * @param string $featured_bundle_type
 *   The paragraph bundle where the featured item would live.
 *   We assume there is only one of these per page...
 *
 * @return bool|int
 *   Node id of the featured item, or FALSE if not found.
 */
function _health_get_featured_item($featured_bundle_type) {
  $node = menu_get_object('node');
  $components = field_get_items('node', $node, 'field_components');
  // Get a list of ids of the components.
  $ids = array();
  foreach ($components as $component) {
    $ids[] = $component['value'];
  }
  $paragraphs = entity_load('paragraphs_item', $ids);
  foreach ($paragraphs as $paragraph) {

    if ($nid = _health_get_featured_item_nid($paragraph, $featured_bundle_type)) {
      return $nid;
    }

    if ($paragraph->bundle == 'two_columns') {
      $column_one = field_get_items('paragraphs_item', $paragraph, 'field_first_column');
      $column_two = field_get_items('paragraphs_item', $paragraph, 'field_second_column');
      $items = entity_load('paragraphs_item', [$column_one[0]['value'], $column_two[0]['value']]);
      foreach ($items as $item) {
        if ($nid = _health_get_featured_item_nid($item, $featured_bundle_type)) {
          return $nid;
        }
      }
    }
  }
  return FALSE;
}

/**
 * Get the featured node id based on a node.
 *
 * @param string $featured
 *   The node to search for a featured item in.
 * @param string $featured_bundle_type
 *   The paragraph bundle where the featured item would live.
 *
 * @return bool|int
 *   Node id or FALSE.
 */
function _health_get_featured_item_nid($featured, $featured_bundle_type) {
  if ($featured->bundle == $featured_bundle_type) {
    switch ($featured_bundle_type) {
      case 'featured_news_events':
        $field = 'field_related';
        break;

      case 'featured_news_events_hp':
        $field = 'field_featured_news_event_hp';
        break;

      case 'featured_resource':
        $field = 'field_topic_featured_resource';
        break;

      case 'featured_resource_hp':
        $field = 'field_related_resource';
        break;

      default:
        $field = '';
        break;
    }
    $featured_item = field_get_items('paragraphs_item', $featured, $field);
    if (!empty($featured_item)) {
      return $featured_item[0]['target_id'];
    }
  }
  return FALSE;
}

/**
 * Replaces non breaking spaces with normal spaces.
 *
 * @param string $markup
 *   HTML markup.
 *
 * @return string
 *   Markup with non breaking spaces replaced.
 */
function _health_replace_nbsp($markup) {
  $markup = htmlentities($markup, NULL, 'utf-8');
  $markup = str_replace("&nbsp;", " ", $markup);
  return html_entity_decode($markup);
}

/**
 * Create a version of a string for file names or ids.
 *
 * @param string $text
 *   The text to convert.
 *
 * @return string
 *   Converted text.
 */
function _health_prepare_filename($text) {
  // Replace anything not normal with a hyphen.
  $clean = strtolower(preg_replace('/[^a-zA-Z\d]+/', '-', $text));
  // Remove any hyphens at the start.
  $clean = preg_replace('/^-/', '', $clean);
  // Remove any hyphens at the end.
  $clean = preg_replace('/-$/', '', $clean);
  // If this was only special characters, base 64 encode it so it is usable.
  if (empty($clean)) {
    return base64_encode($text);
  }
  return $clean;
}

/**
 * Add the total number of nodes in a paragraph to each node.
 *
 * @param array $variables
 *   Preprocess variable.
 * @param string $node_field
 *   Node reference field machine name.
 *
 * @return array
 *   Preprocess variable.
 */
function _health_total_children(array $variables, $node_field) {
  $count = count($variables[$node_field]);
  // Pass total number to each node entity.
  foreach ($variables['content'][$node_field] as $key => $item) {
    if (is_array($item) && isset($item['node'])) {
      foreach ($item['node'] as $node_id => $node) {
        if (is_array($node)) {
          $node['num'] = $count;
          $variables['content'][$node_field][$key]['node'][$node_id] = $node;
        }
      }
    }
  }

  return $variables;
}

/**
 * Add grid class according to the total number of items in a row.
 *
 * @param int $number_of_items
 *   Number of items.
 * @param array $classes_array
 *   The array to store the classes in.
 * @param string $xs_grid
 *   Default class for xs col.
 *
 * @return array
 *   The updated classes array.
 */
function _health_grid_class($number_of_items, array $classes_array, $xs_grid = 'col-xs-12') {
  $classes_array[] = $xs_grid;
  switch ($number_of_items) {
    case 2:
      $classes_array[] = 'col-sm-6';
      break;

    case 3:
      $classes_array[] = 'col-sm-4';
      break;

    case 4:
      $classes_array[] = 'col-sm-6';
      break;

    case 5:
      $classes_array[] = 'col-md-5ths';
      break;

    case 6:
      $classes_array[] = 'col-sm-4';
      break;

    case 7:
    case 8:
      $classes_array[] = 'col-sm-6';
      break;
  }

  return $classes_array;
}

/**
 * Loads a node using a node path.
 *
 * @param string $node_path
 *   The node page, eg node/123.
 *
 * @return bool|object
 *   The node or FALSE.
 */
function _health_load_node_from_node_path($node_path) {
  if (strpos($node_path, 'node/') !== FALSE) {
    $nid = str_replace('node/', '', $node_path);
    if (is_numeric($nid)) {
      if ($node = node_load($nid)) {
        return $node;
      }
    }
  }
  return FALSE;
}

/**
 * Returns a human readable 'ago' (past) or 'in' (future) time.
 *
 * @param string $date
 *   Any date string compatible with strtotime.
 *
 * @return array
 *   text - The friendly text
 *   ago - TRUE|FALSE if this date is in the past or not
 */
function _health_friendly_interval_date($date) {
  $date = strtotime($date);

  if ($date > REQUEST_TIME) {
    $friendly = format_interval($date - REQUEST_TIME, 1);
    return ['text' => $friendly, 'ago' => FALSE];
  }
  else {
    $friendly = format_interval(REQUEST_TIME - $date, 1);
    return ['text' => $friendly, 'ago' => TRUE];
  }
}
