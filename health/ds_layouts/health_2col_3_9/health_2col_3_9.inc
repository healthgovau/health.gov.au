<?php
function ds_health_2col_3_9() {
  return array(
    'label' => t('Health - 2 column - 25% / 75%'),
    'regions' => array(
      'left' => t('Left'),
      'right' => t('Right'),
    ),
    // Add this line if there is a default css file.
    'css' => TRUE,
    // Add this line if you're using DS 2.x for icon preview
    'image' => TRUE,
  );
}