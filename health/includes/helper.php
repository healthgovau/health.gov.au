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
 * Helper function to add vueJS directive to multi elements.
 *
 * @param array $form
 *   From array.
 *
 * @param array $element_names
 *   Form elements with ID names.
 */
function _health_add_vuejs_directive(&$form, $element_names) {
  foreach ($element_names as $key => $id) {
    $form['submitted'][$key]['#prefix'] = '<div id="'.$id.'">';
    $form['submitted'][$key]['#attributes']['v-model'] = 'message';
    $form['submitted'][$key]['#attributes']['maxlength'] = '1200';
    $form['submitted'][$key]['#suffix'] = '<p>{{1200 - message.length}} characters remaining</p></div>';
  }
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
}

/**
 * Generate a link to a content types listing page.
 *
 * @param string $content_type
 *   Machine name of the content type.
 *
 * @return string
 *   HTML link.
 */
function _health_generate_listing_link($content_type) {

  // Hold the path parts... HODOR!
  $path_parts = array();

  // Add category.
  switch($content_type) {
    case 'publication':
    case 'image':
    case 'video':
      $path_parts[] = 'resources';
      break;
    case 'event':
    case 'news_article':
      $path_parts[] = 'news-and-events';
      break;
  }

  // Add type plural.
  switch($content_type) {
    case 'publication':
      $path_parts[] = 'publications';
      break;
    case 'image':
      $path_parts[] = 'images';
      break;
    case 'video':
      $path_parts[] = 'videos';
      break;
  }

  // Get the list of content type names.
  $names = node_type_get_names();

  // Return a link to the listing.
  return l($names[$content_type], implode('/', $path_parts));
}
