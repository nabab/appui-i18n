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
  $success = true;
  $data = [];
  if ( empty($model->data['id_option']) ){
    $data = $translation->get_num_options();
  }
  else{
    $data = $translation->get_num_option($model->data['id_option']);
    //die(var_dump('hrere', $model->data));
  }
  $options = $translation->get_num_options();
  
  return [
    'data' => $data,
    'success' => $success,
    'configured_langs' => $configured_langs
  ];
}

return [
  'success' => false
];