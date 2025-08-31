<?php

/** @var bbn\Mvc\Model $model */



$translation = new \bbn\Appui\I18n($model->db);

if ( !empty($model->data['id_project']) && ( $model->data['id_project'] === 'options' ) ){
  $configured_langs = [];

  $primaries = $translation->getPrimariesLangs();
  foreach ($primaries as $p ){
    $configured_langs[] = $p['id'];
  }
  $success = true;
  $data = [];
  if ( empty($model->data['id_option']) ){
    $data = $translation->getNumOptions();
  }
  else{
    $data = $translation->getNumOption($model->data['id_option']);
    //die(var_dump('hrere', $model->data));
  }
  $options = $translation->getNumOptions();
  
  return [
    'data' => $data,
    'success' => $success,
    'configured_langs' => $configured_langs
  ];
}

return [
  'success' => false
];