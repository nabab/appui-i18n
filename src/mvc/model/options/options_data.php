<?php
/*
 * Describe what it does!
 *
 **/

/** @var $this \bbn\mvc\model*/



$translation = new \bbn\appui\i18n($model->db);

if ( !empty($model->data['id_project']) && ( $model->data['id_project'] === 'options' ) ){
  $configured_langs = [];

  $primaries = $translation->get_primaries_langs();
  foreach ($primaries as $p ){
    $configured_langs[] = $p['id'];
  }

  return [
    'data' => $translation->get_num_options(),
    'success' => true,
    'configured_langs' => $configured_langs
  ];
}
return [
  'success' => false
];