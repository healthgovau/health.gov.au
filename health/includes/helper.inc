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
  $form['#attached']['js'][] = drupal_get_path('theme', 'health') .'/js/dist/health.fieldcounter.min.js';

  // Add in the fields that we want to have this functionality.
  $form['#attached']['js']['(function ($) {$(document).ready(function(){'.$js.'});})(jQuery);'] = [
    'type' => 'inline',
    'scope' => 'footer',
    'weight' => 0,
  ];
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
 * Helper function to generate public hp switcher render array for public topic
 * page.
 *
 * @param $variables
 * @return array
 */
function _health_get_render_elements_for_public_topic_page($variables) {
  $title = 'Health professionals';
  $text = $variables['bean']->field_bean_body[LANGUAGE_NONE][0]['value'];

  // Get the current path and work back to the topic element.
  $path = drupal_get_path_alias();
  $parts = explode('/',$path);
  $url = '/topics/' . $parts[1] . '/health-professionals';

  return [
    'title' => $title,
    'text' => $text,
    'url' => $url,
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
function _health_get_render_elements_for_hp_topic_page($variables) {
  $title = 'General public';
  $text = $variables['bean']->field_bean_text[LANGUAGE_NONE][0]['value'];

  // Get the current path and work back to the topic element.
  $path = drupal_get_path_alias();
  $parts = explode('/',$path);
  $url = '/topics/' . $parts[1];

  return [
    'title' => $title,
    'text' => $text,
    'url' => $url,
    'font' => 'fa-medkit',
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
function _health_label_display($variables, $field) {
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
        $field_variables['classes'] .= ' field-label-inline';
        break;
    }
  }
  return $field_variables;
}

/**
 * Get the weight of a Display Suite field using the current view mode.
 *
 * @param $variables
 *   Preprocess variables.
 * @param $field
 *   The field to get the weight for.
 * @return int
 *   The weight
 */
function _health_get_ds_field_weight($variables, $field) {
  $field_settings = ds_get_field_settings('node', $variables['type'], $variables['view_mode']);
  return $field_settings[$field]['weight'];
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
 *
 * @return string
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
 * @param $featured_bundle_type
 *   The paragraph bundle where the featured item would live.
 *   We assume there is only one of these per page...
 *
 * @return bool
 */
function _health_get_featured_item($featured_bundle_type) {
  $node = menu_get_object('node');
  $components = field_get_items('node', $node, 'field_components');
  // Get a list of ids of the components.
  $ids = array();
  foreach($components as $component) {
    $ids[] = $component['value'];
  }
  $paragraphs = entity_load('paragraphs_item', $ids);
  foreach($paragraphs as $paragraph) {

    if ($nid = _health_get_featured_item_nid($paragraph, $featured_bundle_type)) {
      return $nid;
    }

    if ($paragraph->bundle == 'two_columns') {
      $column_one = field_get_items('paragraphs_item', $paragraph, 'field_first_column');
      $column_two = field_get_items('paragraphs_item', $paragraph, 'field_second_column');
      $items = entity_load('paragraphs_item', [$column_one[0]['value'], $column_two[0]['value']]);
      foreach($items as $item) {
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
 * @param $featured
 *   Node
 * @param $featured_bundle_type
 *   The paragraph bundle where the featured item would live.
 *
 * @return bool
 */
function _health_get_featured_item_nid($featured, $featured_bundle_type) {
  if ($featured->bundle == $featured_bundle_type) {
    switch($featured_bundle_type) {
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
 * @param $markup
 *
 * @return string
 */
function _health_replace_nbsp($markup) {
  $markup = htmlentities($markup, null, 'utf-8');
  $markup = str_replace("&nbsp;", " ", $markup);
  return html_entity_decode($markup);
}

/**
 * @param $name
 *
 * @return mixed|string
 */
function _health_prepare_filename($name) {
  // Replace anything not normal with a hyphen.
  $clean = strtolower(preg_replace('/[^a-zA-Z\d]+/', '-', $name));
  // Remove any hyphens at the start.
  $clean = preg_replace('/^-/', '', $clean);
  // Remove any hyphens at the end.
  $clean = preg_replace('/-$/', '', $clean);
  // If this was only special characters, base 64 encode it so it is usable
  if (empty($clean)) {
    return base64_encode($name);
  }
  return $clean;
}

/**
 * Loads a node using a node path.
 *
 * @param string $node_path
 *   node/123
 *
 * @return bool|mixed
 *   FALSE if not a node path or if node is not found
 *   Otherwise returns node object via node_load().
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
