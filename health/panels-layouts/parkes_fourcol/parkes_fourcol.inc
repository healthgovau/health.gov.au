<?php

// Plugin definition
$plugin = array(
  'title' => t('Parkes: Four column'),
  'category' => t('Columns: 4'),
  'icon' => 'parkes_fourcol.png',
  'theme' => 'parkes_fourcol',
  'css' => 'parkes_fourcol.css',
  'regions' => array(
    'col1' => t('Column one'),
    'col2' => t('Column two'),
    'col3' => t('Column three'),
    'col4' => t('Column four'),
  ),
);
