<?php

/**
 * @file
 * Theme setting callbacks for the Adminimal theme.
 */

/**
 * Implements hook_form_FORM_ID_alter().
 *
 * @param $form
 *   The form.
 * @param $form_state
 *   The form state.
 */
function health_adminimal_form_system_theme_settings_alter(&$form, &$form_state) {

  $form['health_adminimal_content_freeze'] = array(
    '#type' => 'fieldset',
    '#title' => t('Content freeze'),
    '#weight' => 0,
  );

  $form['health_adminimal_content_freeze']['content_freeze_flag'] = array(
    '#type' => 'checkbox',
    '#title' => t('Display content freeze notification'),
    '#description' => t('Enable to notify authors that a content freeze is in place and that they will need to dual author.'),
    '#default_value' => theme_get_setting('content_freeze_flag'),
  );

  $form['health_adminimal_content_freeze']['dual_author_environment'] = array(
    '#type' => 'textfield',
    '#title' => t('Dual author environment'),
    '#description' => t('If a content freeze is active, the URL of the other environment that the authors will need to use'),
    '#default_value' => theme_get_setting('dual_author_environment'),
  );
}
