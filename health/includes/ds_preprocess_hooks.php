<?php

/**
 * This file contains the display suite preprocess hooks.
 * They are called from health_preprocess_node.
 */

/**
 * DS - Content type.
 *
 * @param $variables
 *
 * @return string
 */
function health_preprocess_ds_content_type(&$variables) {
  return _health_generate_listing_link($variables['node']->type);
}

/**
 * Implements DS preprocess field -- table of content.
 *
 * @param $variable
 * 
 * @return string
 */
function health_preprocess_ds_table_of_content(&$variable) {
  if (isset($variable['field_table_of_content'][LANGUAGE_NONE]) && $variable['field_table_of_content'][LANGUAGE_NONE][0]['value'] == 1) {
    // This node is flagged to display table of content.
    drupal_add_js(drupal_get_path('theme', 'health') . '/js/anchorific.min.js');
    drupal_add_js(drupal_get_path('theme', 'health') . '/js/health.toc.js');
    return '<nav class="uikit-inpage-nav-links"><div class="uikit-inpage-nav-links__heading uikit-display-2">In this section</div><div class="toc uikit-link-list"></div></nav>';
  }
}
