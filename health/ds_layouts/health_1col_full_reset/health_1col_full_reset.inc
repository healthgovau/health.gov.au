<?php
function ds_health_1col_full_reset() {
  return array(
    'label' => t('Health - 1 column - Full reset'),
    'regions' => array(
      'ds_content' => t('Content'),
    ),
    // Add this line if there is a default css file.
    'css' => FALSE,
    // Add this line if you're using DS 2.x for icon preview
    'image' => TRUE,
  );
}