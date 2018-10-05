<?php
$success = false;
if ( ( $id_option = $model->data['id_option'] ) && ($id_project = $model->data['id_project']) ){
  $translation = new \bbn\appui\i18n($model->db);

  //overwrite the cache of the table
  $translation->cache_set($id_option, 'get_translations_table',
    $translation->get_translations_table( $id_project, $id_option)
  );
  //get the content of the new cache of the table
  $res = $translation->cache_get($id_option, 'get_translations_table');

  //remake cache of the widget
  $translation->cache_set($id_option, 'get_translations_widget',
    $translation->get_translations_widget($id_project,$id_option)
  );
  $translation->cache_get($id_option, 'get_translations_widget');

  $success = true;
  return [
    'res' => $res,
    'success' => $success
  ];
}