<?php
/**
 * @file
 * Implements a panels layout called govcms_parkes_no_wrapper.
 */

// Plugin definition
$plugin = array(
  'title'       => t('Parkes’s no wrapper'),
  'category'    => t('Columns: 1'),
  'icon'        => 'parkes_no_wrapper.png',

  'theme'       => 'parkes_no_wrapper',

  'regions'     => array(
    'main'      => t('Main'),
  ),

  'settings form'     => 'govcms_parkes_no_wrapper_settings_form',
  'settings validate' => 'govcms_parkes_no_wrapper_settings_validate',
  'settings submit'   => 'govcms_parkes_no_wrapper_settings_submit',
);
