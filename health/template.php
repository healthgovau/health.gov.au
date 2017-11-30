<?php
/**
 * @file
 * Contains the theme's functions to manipulate Drupal's default markup.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728096
 */

// Include inc files.
include_once drupal_get_path('theme', 'health') . '/includes/helper.php';
include_once drupal_get_path('theme', 'health') . '/includes/preprocess_hooks.php';
include_once drupal_get_path('theme', 'health') . '/includes/ds_preprocess_hooks.php';

// Auto-rebuild the theme registry during theme development.
if (theme_get_setting('health_rebuild_registry') && !defined('MAINTENANCE_MODE')) {
  // Rebuild .info data.
  system_rebuild_theme_data();
  // Rebuild theme registry.
  drupal_theme_rebuild();
  // Turn on template debugging.
  $GLOBALS['conf']['theme_debug'] = TRUE;
}


/**
 * Implements HOOK_theme().
 */
function health_theme(&$existing, $type, $theme, $path) {
  // If we are auto-rebuilding the theme registry, warn about the feature.
  if (
    // Don't display on update.php or install.php.
    !defined('MAINTENANCE_MODE')
    // Only display for site config admins.
    && function_exists('user_access') && user_access('administer site configuration')
    && theme_get_setting('health_rebuild_registry')
    // Always display in the admin section, otherwise limit to three per hour.
    && (arg(0) == 'admin' || flood_is_allowed($GLOBALS['theme'] . '_rebuild_registry_warning', 3))
  ) {
    flood_register_event($GLOBALS['theme'] . '_rebuild_registry_warning');
    drupal_set_message(t('For easier theme development, the theme registry is being rebuilt on every page request. It is <em>extremely</em> important to <a href="!link">turn off this feature</a> on production websites.', array('!link' => url('admin/appearance/settings/' . $GLOBALS['theme']))), 'warning', FALSE);
  }

  // hook_theme() expects an array, so return an empty one.
  return array();
}

/**
 * Implements THEME_breadcrumb().
 */
function health_breadcrumb($variables) {
  // Override breadcrumb if current page is a search view page.
  $query_string = drupal_get_query_parameters();
  if (isset($query_string['f'])) {
    // Find out if it is a topic related page.
    foreach ($query_string['f'] as $string) {
      if (strpos($string, 'field_related_health_topic') !== FALSE) {
        // Do not override the title if there are more than one topic in the filter.
        if (substr_count(implode('&', $query_string['f']), 'field_related_health_topic') == 1) {
          $topic_id = explode(':', $string)[1];
          $topic_node = node_load($topic_id);
          array_splice($variables['breadcrumb'], 1, 0, l($topic_node->title, $topic_node->path['alias']));
        }
      }
    }
  }
  
  // Build the breadcrumb trail.
  // We replace the default breadcrumb output for a couple of key reasons:
  //  - should be wrapped in a nav tag
  //  - breadcrumb items should be in an ordered list
  $output = '<nav class="breadcrumbs" role="navigation">';
  $output .= '<h2 class="element-invisible">' . $variables['title'] . '</h2>';
  $output .= '<ol class="breadcrumbs__list">';
  // If home is set from the setting, we display home link.
  if (isset($variables['crumbs_trail']['front'])) {
    $output .= '<li><a href="/">Home</a></li><li>';
  }
  else {
    $output .= '<li>';
  }
  $output .= implode('</li><li>', $variables['breadcrumb']);
  $output .= '</li></ol></nav>';

  return $output;
}


/**
 * Override or insert variables into the html templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("html" in this case.)
 */
function health_process_html(&$variables, $hook) {
  // Flatten out html_attributes.
  $variables['html_attributes'] = drupal_attributes($variables['html_attributes_array']);
}


/**
 * Override or insert variables in the html_tag theme function.
 */
function health_process_html_tag(&$variables) {
  $tag = &$variables['element'];

  if ($tag['#tag'] == 'style' || $tag['#tag'] == 'script') {
    // Remove redundant CDATA comments.
    unset($tag['#value_prefix'], $tag['#value_suffix']);

    // Remove redundant type attribute.
    if (isset($tag['#attributes']['type']) && $tag['#attributes']['type'] !== 'text/ng-template') {
      unset($tag['#attributes']['type']);
    }

    // Remove media="all" but leave others unaffected.
    if (isset($tag['#attributes']['media']) && $tag['#attributes']['media'] === 'all') {
      unset($tag['#attributes']['media']);
    }
  }
}

/**
 * Implement hook_html_head_alter().
 */
function health_html_head_alter(&$head) {
  // Simplify the meta tag for character encoding.
  if (isset($head['system_meta_content_type']['#attributes']['content'])) {
    $head['system_meta_content_type']['#attributes'] = array('charset' => str_replace('text/html; charset=', '', $head['system_meta_content_type']['#attributes']['content']));
  }
}

/**
 * Override or insert variables into the maintenance page template.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("maintenance_page" in this case.)
 */
function health_process_maintenance_page(&$variables, $hook) {
  health_process_html($variables, $hook);
  // Ensure default regions get a variable. Theme authors often forget to remove
  // a deleted region's variable in maintenance-page.tpl.
  foreach (array('header', 'navigation', 'highlighted', 'help', 'content', 'sidebar_first', 'sidebar_second', 'footer', 'bottom') as $region) {
    if (!isset($variables[$region])) {
      $variables[$region] = '';
    }
  }
}

/**
 * Override or insert variables into the block templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case.)
 */
function health_process_block(&$variables, $hook) {
  // Drupal 7 should use a $title variable instead of $block->subject.
  $variables['title'] = isset($variables['block']->subject) ? $variables['block']->subject : '';
}

/**
 * Implements hook_page_alter().
 *
 * Look for the last block in the region. This is impossible to determine from
 * within a preprocess_block function.
 *
 * @param $page
 *   Nested array of renderable elements that make up the page.
 */
function health_page_alter(&$page) {
  // Look in each visible region for blocks.
  foreach (system_region_list($GLOBALS['theme'], REGIONS_VISIBLE) as $region => $name) {
    if (!empty($page[$region])) {
      // Find the last block in the region.
      $blocks = array_reverse(element_children($page[$region]));
      while ($blocks && !isset($page[$region][$blocks[0]]['#block'])) {
        array_shift($blocks);
      }
      if ($blocks) {
        $page[$region][$blocks[0]]['#block']->last_in_region = TRUE;
      }
    }
  }
}

/**
 * Implements hook_form_BASE_FORM_ID_alter().
 *
 * Prevent user-facing field styling from screwing up node edit forms by
 * renaming the classes on the node edit form's field wrappers.
 */
function health_form_node_form_alter(&$form, &$form_state, $form_id) {
  // Remove if #1245218 is backported to D7 core.
  foreach (array_keys($form) as $item) {
    if (strpos($item, 'field_') === 0) {
      if (!empty($form[$item]['#attributes']['class'])) {
        foreach ($form[$item]['#attributes']['class'] as &$class) {
          // Core bug: the field-type-text-with-summary class is used as a JS hook.
          if ($class != 'field-type-text-with-summary' && strpos($class, 'field-type-') === 0 || strpos($class, 'field-name-') === 0) {
            // Make the class different from that used in theme_field().
            $class = 'form-' . $class;
          }
        }
      }
    }
  }
}

/**
 * Implements hook_form_BASE_FORM_ID_alter().
 * 
 * Append selected filter links to 'contains' search box. 
 */
function health_form_views_exposed_form_alter(&$form, &$form_state, $form_id) {
  if (isset($form['#info']['filter-search_api_views_fulltext'])) {
    $query_string = drupal_get_query_parameters();
    if (isset($query_string['f']) && !empty($query_string['f'])) {
      // Find selected filters.
      $output = '<div id="edit-custom-remove" class="form-item form-type-item">
<label for="edit-custom-remove">Filters </label>
<div class="facet-remove">';
      $links = '';
      $url = '';
      foreach ($query_string['f'] as $key => $param) {
        $query_string_modified = $query_string['f'];
        $filter_name = _health_find_facet_filter_name($param);
        // Re compose query string.
        unset($query_string_modified[$key]);
        if (isset($query_string['search_api_views_fulltext'])) {
          $url .= 'search_api_views_fulltext=' . $query_string['search_api_views_fulltext'];
        }
        foreach ($query_string_modified as $key_1 => $item) {
          $url .= '&f[' . $key_1 . ']=' . $item;
        }

        $links .= '<a class="facet-remove-link" href="/' . current_path() . '?' . $url . '">' . $filter_name . ' <span class="facet-remove-link__icon">X</span></a>';
      }
      
      $output .= $links . '</div></div>';

      $form['#suffix'] = $output;
    }
  }
}

/**
 * Implements hook_form_alter().
 * @param $form
 */
function health_form_alter(&$form, &$form_state, $form_id) {

  // Alter user feedback webform.
  if (isset($form['#node'])) {
    $node = $form['#node'];
    // Find user feedback form by title.
    if ($node->title == 'Provide feedback' && $node->type == 'webform') {
      // Add referrer.
      $referrer_url = $_SERVER['HTTP_REFERER'];
      $form['submitted']['is_this_feedback_for']['#type'] = 'select';
      $form['submitted']['is_this_feedback_for']['#options'] = array(
        'The whole website' => t('The whole website'),
        $referrer_url => t('The page you were just on'),
      );
      // Remove the default validate for the new option in the select list.
      // @todo Use another approach to add the option dynamically.
      $form['#validate'] = array();
      
      // Attach vuejs and countdown js.
      $form['#attached']['js'][] = path_to_theme() . '/js/health.feedback.countdown.js';

      // Add vue directive.
      _health_add_vuejs_directive($form, [
        'feedback' => 'feedback-textarea', 
        'suggestions_for_improvement' => 'improvement-textarea',
      ]);
    }
  }
}

/**
 * Implements hook_views_pre_render().
 *
 * Override or insert variables into the views pre render.
 *
 * @param array $view
 *   The view object about to be processed.
 */
function health_views_pre_render(&$view) {
  // Override title for search views based on the faceted filter.
  if ($view->base_field == 'search_api_id') {
    $query_string = drupal_get_query_parameters();
    if (isset($query_string['f'])) {
      // Find out if it is a topic related page.
      foreach ($query_string['f'] as $string) {
        if (strpos($string, 'field_related_health_topic') !== FALSE) {
          // Do not override the title if there are more than one topic in the filter.
          if (substr_count(implode('&', $query_string['f']), 'field_related_health_topic') == 1) {
            $topic_id = explode(':', $string)[1];
            $topic_node = node_load($topic_id);
            $view->build_info['title'] = $topic_node->title . ' - ' . $view->name;
          }
        }
      }
    }
  }
}

/**
 * Alters the default Panels render callback so it removes the panel separator.
 */
function health_panels_default_style_render_region($variables) {
  return implode('', $variables['panes']);
}

/**
 * Alters the checkbox and radio buttons so the markup is usable for the uikit.
 */
function health_form_element_label($variables) {
  $element = $variables['element'];
  // This is also used in the installer, pre-database setup.
  $t = get_t();

  // If title and required marker are both empty, output no label.
  if ((!isset($element['#title']) || $element['#title'] === '') && empty($element['#required'])) {
    return '';
  }

  // If the element is required, a required marker is appended to the label.
  $required = !empty($element['#required']) ? theme('form_required_marker', array('element' => $element)) : '';
  $title = key_exists('#title', $element) ? filter_xss_admin($element['#title']) : '';

  $attributes = array();
  // Show label only to screen readers to avoid disruption in visual flows.
  if ($element['#title_display'] == 'invisible') {
    $attributes['class'] = 'element-invisible';
  }

  if (!empty($element['#id'])) {
    $attributes['for'] = $element['#id'];
  }

  $output = '';

  // Find out what type of form element this is.
  $type = !empty($element['#type']) ? $element['#type'] : FALSE;
  $checkbox = $type && $type === 'checkbox';
  $radio = $type && $type === 'radio';

  // Construct the title.
  $title = $t('!title !required', array('!title' => $title, '!required' => $required));

  if ($checkbox || $radio) {
    // Checkboxes and radios need a span around them to support UI kit styling.
    $output .= $element['#children'];
    $output .= '<span class="input__text">';
    $output .= $title;
    $output .= '</span>';
  } else {
    // The leading whitespace helps visually separate fields from inline labels.
    $output = $title;
  }
  return ' <label' . drupal_attributes($attributes) . '>' . $output . "</label>\n";
}

/**
 * Alters the checkbox and radio buttons so the markup is usable for the uikit.
 */
function health_form_element(&$variables) {
  $element = &$variables['element'];
  $name = !empty($element['#name']) ? $element['#name'] : FALSE;
  $type = !empty($element['#type']) ? $element['#type'] : FALSE;
  $checkbox = $type && $type === 'checkbox';
  $radio = $type && $type === 'radio';

  // Create an attributes array for the wrapping container.
  if (empty($element['#wrapper_attributes'])) {
    $element['#wrapper_attributes'] = array();
  }
  $wrapper_attributes = &$element['#wrapper_attributes'];

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  // Add wrapper ID for 'item' type.
  if ($type && $type === 'item' && !empty($element['#markup']) && !empty($element['#id'])) {
    $wrapper_attributes['id'] = $element['#id'];
  }

  // Check for errors and set correct error class.
  if ((isset($element['#parents']) && form_get_error($element) !== NULL) || (!empty($element['#required']))) {
    $wrapper_attributes['class'][] = 'has-error';
  }

  // Add necessary classes to wrapper container.
  $wrapper_attributes['class'][] = 'form-item';
  if ($name) {
    $wrapper_attributes['class'][] = 'form-item-' . drupal_html_class($name);
  }
  if ($type) {
    $wrapper_attributes['class'][] = 'form-type-' . drupal_html_class($type);
  }
  if (!empty($element['#attributes']['disabled'])) {
    $wrapper_attributes['class'][] = 'form-disabled';
  }
  if (!empty($element['#autocomplete_path']) && drupal_valid_path($element['#autocomplete_path'])) {
    $wrapper_attributes['class'][] = 'form-autocomplete';
  }

  // Checkboxes and radios do no receive the 'form-group' class, instead they
  // simply have their own classes.
  if ($checkbox || $radio) {
    $wrapper_attributes['class'][] = drupal_html_class($type);
  }
  elseif ($type && $type !== 'hidden') {
    $wrapper_attributes['class'][] = 'form-group';
  }

  // Create a render array for the form element.
  $build = array(
    '#theme_wrappers' => array('container__form_element'),
    '#attributes' => $wrapper_attributes,
  );

  // Render the label for the form element.
  $build['label'] = array(
    '#markup' => theme('form_element_label', $variables),
    '#weight' => $element['#title_display'] === 'before' ? 0 : 2,
  );

  // Checkboxes and radios render the input element inside the label. If the
  // element is neither of those, then the input element must be rendered here.
  if (!$checkbox && !$radio) {
    $prefix = isset($element['#field_prefix']) ? $element['#field_prefix'] : '';
    $suffix = isset($element['#field_suffix']) ? $element['#field_suffix'] : '';
    if ((!empty($prefix) || !empty($suffix)) && (!empty($element['#input_group']) || !empty($element['#input_group_button']))) {
      if (!empty($element['#field_prefix'])) {
        $prefix = '<span class="input-group-' . (!empty($element['#input_group_button']) ? 'btn' : 'addon') . '">' . $prefix . '</span>';
      }
      if (!empty($element['#field_suffix'])) {
        $suffix = '<span class="input-group-' . (!empty($element['#input_group_button']) ? 'btn' : 'addon') . '">' . $suffix . '</span>';
      }

      // Add a wrapping container around the elements.
      $input_group_attributes['class'][] = 'input-group';
      $prefix = '<div' . drupal_attributes($input_group_attributes) . '>' . $prefix;
      $suffix .= '</div>';
    }

    // Build the form element.
    $build['element'] = array(
      '#markup' => $element['#children'],
      '#prefix' => !empty($prefix) ? $prefix : NULL,
      '#suffix' => !empty($suffix) ? $suffix : NULL,
      '#weight' => 1,
    );
  }

  // Construct the element's description markup.
  if (!empty($element['#description'])) {
    $build['description'] = array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => array('help-block'),
      ),
      '#weight' => isset($element['#description_display']) && $element['#description_display'] === 'before' ? 0 : 20,
      0 => array('#markup' => filter_xss_admin($element['#description'])),
    );
  }

  // Render the form element build array.
  return drupal_render($build);
}

/**
 * Wraps a div around the select element so we can use the :after attribute to consistently style the element.
 */
function health_select($variables) {
  $element = $variables['element'];
  element_set_attributes($element, array('id', 'name', 'size'));
  _form_set_class($element, array('form-select'));

  return '<div class="form-select--wrapper"><select' . drupal_attributes($element['#attributes']) . '>' . form_select_options($element) . '</select></div>';
}

/**
 * Implements hook_js_alter().
 *
 * Perform necessary alterations to the JavaScript before it is presented on the page.
 *
 * @param array $javascript
 *   An array of all JavaScript being presented on the page.
 */
function health_js_alter(&$javascript) {
  // Replace jQuery version on non admin pages.
  $replace_jquery = TRUE;

  // Array of additional admin paths
  $extra_admin_paths = array(
    'node/*/edit',
    'node/add/*',
    'media/browser*',
    'media/ajax*'
  );

  // Do not apply to admin pages that may require lower version of jQuery provided by Drupal core
  if (path_is_admin(current_path())) {
    $replace_jquery = FALSE;
  } else {
    foreach ($extra_admin_paths as $extra_admin_path) {
      if (drupal_match_path(current_path(), $extra_admin_path)) {
        $replace_jquery = FALSE;
      }
    }
  }

  if ($replace_jquery) {
    $javascript['misc/jquery.js']['data'] = drupal_get_path('theme', 'health') . '/js/jquery.min.js';
    // Add pancake JS for none admin pages.
    drupal_add_js(drupal_get_path('theme', 'health') . '/pancake/js/pancake.js');
  }
}

/**
 * Implements hook_file_entity_download_link().
 *
 * Output differently for Image content type.
 *
 * @param $variables
 *
 * @return string
 */
function health_file_entity_download_link($variables) {

  $nid = arg(1);
  if (is_numeric($nid)) {
    $node = node_load($nid);

    // If this is not an image or publication content type, do the normal formatting.
    if ($node->type != 'image' && $node->type != 'publication') {
      return theme_file_entity_download_link($variables);
    } else {

      // Grab the file.
      $file = $variables['file'];

      // Get file title to use in analytics and accessibility.
      // Default is the resource title.
      $title = $node->title;

      // Publications.
      if ($node->type == 'publication') {
        $docs = $node->field_resource_documents[$node->language];
        foreach ($docs as $doc) {
          $entities = entity_load('paragraphs_item', [$doc['value']]);
          if (!empty($entities)) {
            $para_documents = array_pop($entities);
            foreach ($para_documents->field_resource_document[LANGUAGE_NONE] as $resource_document) {
              $entities = entity_load('paragraphs_item', [$resource_document['value']]);
              if (!empty($entities)) {
                $para_document = array_pop($entities);
                if ($para_document->field_file[LANGUAGE_NONE][0]['fid'] == $file->fid) {
                  if (count($docs) > 1) { // Multiple document parts.
                    $title .= ': ' . $para_documents->field_resource_file_title[LANGUAGE_NONE][0]['value'];
                  }
                  // Get page count.
                  if (isset($para_document->field_resource_file_pages)) {
                    $no_of_pages = $para_document->field_resource_file_pages[LANGUAGE_NONE][0]['value'];
                  }
                }
              }
            }
          }
        }
      }

      // Images.
      if ($node->type == 'image') {
        $docs = $node->field_para_images[$node->language];
        foreach ($docs as $doc) {
          $entities = entity_load('paragraphs_item', [$doc['value']]);
          if (!empty($entities)) {
            $para_documents = array_pop($entities);
            if ($para_documents->field_file[LANGUAGE_NONE][0]['fid'] == $file->fid) {
              // Get sizing.
              if (isset($para_documents->field_paragraph_title)) {
                $size = $para_documents->field_paragraph_title[LANGUAGE_NONE][0]['value'];
              }
            }
          }
        }
      }

      // Construct the link.
      $variables['text'] = '<div class="file__link">Download <span>' . $title . ' as</span> ' . health_get_friendly_mime($file->filemime) . '</div>';

      // Add metatdata (file size, image size, no of pages)
      $variables['text'].= '<span class="file__meta"> - ' . format_size($file->filesize);
      if (isset($no_of_pages)) {
        $variables['text'].= ', ' . $no_of_pages . ' pages';
      }
      if (isset($size)) {
        $variables['text'].= ', ' . $size;
      }
      $variables['text'].= '</span>';

      // Get the icon.
      $icon_directory = $variables['icon_directory'];
      $icon = theme('file_icon', array('file' => $file, 'icon_directory' => $icon_directory));

      // Get the path to the file.
      $uri = file_entity_download_uri($file);

      // Set options as per anchor format described at
      // http://microformats.org/wiki/file-format-examples
      $uri['options']['attributes']['type'] = $file->filemime . '; length=' . $file->filesize;
      $uri['options']['html'] = TRUE;

      // Add filename attribute for analytics.
      $uri['options']['attributes']['data-filename'] = $title;
      $uri['options']['attributes']['data-filetype'] = $file->filemime;

      // Output the link.
      $output = '<span class="file"> ' . $icon . ' ' . l($variables['text'], $uri['path'], $uri['options']) . '</span>';

      return $output;
    }
  }

  return theme_file_entity_download_link($variables);

}

/**
 * Implements theme_webform_element().
 *
 * @param $variables
 *
 * @return string
 */
function health_webform_element($variables) {
  // Ensure defaults.
  $variables['element'] += array(
    '#title_display' => 'before',
  );

  $element = $variables['element'];

  // All elements using this for display only are given the "display" type.
  if (isset($element['#format']) && $element['#format'] == 'html') {
    $type = 'display';
  }
  else {
    $type = (isset($element['#type']) && !in_array($element['#type'], array('markup', 'textfield', 'webform_email', 'webform_number'))) ? $element['#type'] : $element['#webform_component']['type'];
  }

  // Convert the parents array into a string, excluding the "submitted" wrapper.
  $nested_level = $element['#parents'][0] == 'submitted' ? 1 : 0;
  $parents = str_replace('_', '-', implode('--', array_slice($element['#parents'], $nested_level)));

  $wrapper_attributes = isset($element['#wrapper_attributes']) ? $element['#wrapper_attributes'] : array('class' => array());
  $wrapper_classes = array(
    'form-item',
    'webform-component',
    'webform-component-' . $type,
  );
  if (isset($element['#title_display']) && strcmp($element['#title_display'], 'inline') === 0) {
    $wrapper_classes[] = 'webform-container-inline';
  }
  $wrapper_attributes['class'] = array_merge($wrapper_classes, $wrapper_attributes['class']);
  $wrapper_attributes['id'] = 'webform-component-' . $parents;
  $output = '<div ' . drupal_attributes($wrapper_attributes) . '>' . "\n";

  // If #title_display is none, set it to invisible instead - none only used if
  // we have no title at all to use.
  if ($element['#title_display'] == 'none') {
    $variables['element']['#title_display'] = 'invisible';
    $element['#title_display'] = 'invisible';
    if (empty($element['#attributes']['title']) && !empty($element['#title'])) {
      $element['#attributes']['title'] = $element['#title'];
    }
  }
  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }
  $prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . _webform_filter_xss($element['#field_prefix']) . '</span> ' : '';
  $suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . _webform_filter_xss($element['#field_suffix']) . '</span>' : '';

  // Description text.
  // Always output description text between the label and field.
  $description = '';
  if (!empty($element['#description'])) {
    $description = ' <div class="description">' . $element['#description'] . "</div>\n";
  }

  switch ($element['#title_display']) {
    case 'inline':
    case 'before':
    case 'invisible':
      $output .= ' ' . theme('form_element_label', $variables);
      $output .= $description;
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;

    case 'after':
      $output .= ' ' . $prefix . $element['#children'] . $suffix;
      $output .= $description;
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output .= $description;
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  $output .= "</div>\n";

  return $output;
}
