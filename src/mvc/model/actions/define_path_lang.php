<?php
/** @var $this \bbn\Mvc\Model*/

//set the property (source) language for the path, if a cached model of the widget exists it deletes it and creates a new one
if ( isset( $model->data['language'] ) && $model->data['id_option'] &&  !empty($model->data['id_project']) ) {
  $translation = new \bbn\Appui\I18n($model->db, $model->data['id_project']);

  $model->inc->options->setProp($model->data['id_option'], ['language' => $model->data['language']]);
  $data_widget = $translation->getTranslationsWidget($model->data['id_option']);


  $tmp = $translation->getTranslationsTable($model->data['id_project'], $model->data['id_option']);

  $tmp2 = $translation->cacheSet($model->data['id_option'], 'get_translations_table',
    $tmp
  );
  $strings = $translation->cacheGet($model->data['id_option'], 'get_translations_table');

	return [
    'data_widget' => $data_widget,
    'success' => true
  ];
}
