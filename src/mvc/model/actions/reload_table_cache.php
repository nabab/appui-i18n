<?php
$success = false;
if ( ( $id_option = $model->data['id_option'] ) && ($id_project = $model->data['id_project']) ){
  $translation = new \bbn\Appui\I18n($model->db, $id_project);

  //overwrite the cache of the table
  $translation->cacheSet($id_option, 'get_translations_table',
    $translation->getTranslationsTable( $id_project, $id_option)
  );
  //get the content of the new cache of the table
  $res = $translation->cacheGet($id_option, 'get_translations_table');

  //remake cache of the widget
  $translation->cacheSet($id_option, 'get_translations_widget',
    $translation->getTranslationsWidget($id_project,$id_option)
  );
  $translation->cacheGet($id_option, 'get_translations_widget');

  $success = true;
  return [
    'res' => $res,
    'success' => $success
  ];
}