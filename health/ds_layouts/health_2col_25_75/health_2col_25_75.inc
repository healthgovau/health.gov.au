<?php
function ds_health_2col_25_75() {
  return array(
    'label' => t('Health - 2 column - 25% / 75%'),
    'regions' => array(
      'left' => t('Left'),
      'right' => t('Right'),
    ),
    // Add this line if there is a default css file.
    'css' => FALSE,
    // Add this line if you're using DS 2.x for icon preview
    'image' => TRUE,
  );
}