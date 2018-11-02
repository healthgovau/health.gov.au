<?php
/**
 * Implements hook_form_system_theme_settings_alter() function.
 */
function health_form_system_theme_settings_alter(&$form, $form_state, $form_id = NULL) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }

  // Support.
  $form['support'] = array(
    '#type'          => 'fieldset',
    '#title'         => t('Accessibility and support settings'),
  );
  $form['support']['health_meta'] = array(
    '#type'          => 'checkboxes',
    '#title'         => t('Add HTML5 and responsive scripts and meta tags to every page.'),
    '#default_value' => theme_get_setting('health_meta'),
    '#options'       => array(
                          'html5' => t('Add HTML5 shim JavaScript to add support to IE 6-8.'),
                          'meta' => t('Add meta tags to support responsive design on mobile devices.'),
                        ),
    '#description'   => t('IE 6-8 require a JavaScript polyfill solution to add basic support of HTML5. Mobile devices require a few meta tags for responsive designs.'),
  );

  // Layout
  $form['layout'] = array(
    '#type'          => 'fieldset',
    '#title'         => t('Layout'),
  );
  $content_types = node_type_get_types();
  $options = [];
  foreach ($content_types as $key => $content_type) {
    $options[$key] = $content_type->name;
  }
  $form['layout']['full_content_types'] = array(
    '#type' => 'checkboxes',
    '#options' => $options,
    '#title' => t('Full width'),
    '#default_value' => theme_get_setting('full_content_types'),
    '#description' => t('Select which content type should use full width grid.'),
  );

  // Theme development.
  $form['themedev'] = array(
    '#type'          => 'fieldset',
    '#title'         => t('Theme development settings'),
  );
  $form['themedev']['health_rebuild_registry'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Rebuild theme registry and output template debugging on every page.'),
    '#default_value' => theme_get_setting('health_rebuild_registry'),
    '#description'   => t('During theme development, it can be very useful to continuously <a href="!link">rebuild the theme registry</a> and to output template debugging HTML comments. WARNING: this is a huge performance penalty and must be turned off on production websites.', array('!link' => 'https://drupal.org/node/173880#theme-registry')),
  );

  // Layout
  $form['mapping'] = array(
    '#type'          => 'fieldset',
    '#title'         => t('Mapping'),
  );

  $form['mapping']['google_maps_client'] = array(
    '#type' => 'textfield',
    '#title' => t('Google Maps client ID'),
    '#default_value' => theme_get_setting('google_maps_client'),
  );
}
