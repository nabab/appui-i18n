<?php
/*
 * Describe what it does!
 *
 **/

/** @var $this \bbn\mvc\model*/
$translation = new \bbn\appui\i18n($model->db);

if ( !empty($model->data['id_project']) && ( $model->data['id_project'] === 'options' ) ){
  return [
    'path' => $translation->get_options(),
    'success' => true
  ];
}
return [
  'success' => false
];