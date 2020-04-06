<?php
/** @var $this \bbn\mvc\model*/

//set the property (source) language for the path, if a cached model of the widget exists it deletes it and creates a new one
if ( isset( $model->data['language'] ) && $model->data['id_option'] &&  !empty($model->data['id_project']) ) {
  $translation = new \bbn\appui\i18n($model->db, $model->data['id_project']);

  $model->inc->options->set_prop($model->data['id_option'], ['language' => $model->data['language']]);
  $data_widget = $translation->get_translations_widget($model->data['id_project'],$model->data['id_option']);


  $tmp = $translation->get_translations_table($model->data['id_project'], $model->data['id_option']);

  $tmp2 = $translation->cache_set($model->data['id_option'], 'get_translations_table',
    $tmp
  );
  $strings = $translation->cache_get($model->data['id_option'], 'get_translations_table');

	return [
    'data_widget' => $data_widget,
    'success' => true
  ];
}
