<?php
function ds_health_3col_stacked() {
  return array(
    'label' => t('Health - 3 column stacked - Grid - Flexible'),
    'regions' => array(
      'top' => t('Top'),
      'left' => t('Left'),
      'centre' => t('Centre'),
      'right' => t('Right'),
      'bottom' => t('Bottom'),
    ),
    // Add this line if there is a default css file.
    'css' => FALSE,
    // Add this line if you're using DS 2.x for icon preview
    'image' => TRUE,
  );
}