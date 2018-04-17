<?php
/*
 * Describe what it does!
 *
 **/

/** @var $this \bbn\mvc\model*/

$timer = new \bbn\util\timer();
/** @var  $projects array of projects*/
$timer->start('1');
/** @var (array) $projects from db*/
$projects = $model->get_model(APPUI_I18N_ROOT.'page')['projects'];

$timer->stop('1');
//the first time the dashboard is loaded it returns $res empty and $success null
$res = [];
$success = null;
/** @var  $translation instantiate the class i18n */
$translation = new \bbn\appui\i18n($model->db);

foreach ( $projects as $i => $p ){
  if ( $model->data['id_project'] === $projects[$i]['id'] ){
    $timer->start('2');
    /** takes the current project from projects array */
    $project = $projects[$i];
    /** @var  $project_class instantiate the class of project */
    $project_class = new \bbn\appui\project($model->db, $projects[$i]['id']);
    $configured_langs = [];

    /** takes the langs configured in db for the project translation */
    foreach ( $project_class->get_langs() as $p ){
      $configured_langs[] = $p['id'];
    }
    $timer->stop('2');
    $timer->start('3');
    foreach ( $project['path'] as $idx => $pa ){
      /** for every project takes the full option of each path */
      $res[$idx] = $model->inc->options->option($projects[$i]['path'][$idx]['id_option']);

      /** if language is set takes the cached_model_of the widget */
      if ( isset( $res[$idx]['language'] ) ){
        //takes the model of the widget from cache if it exists else creates one
        $res[$idx]['data_widget'] = $model->get_cached_model(APPUI_I18N_ROOT.'page/data/widgets', ['id_option' => $res[$idx]['id']], 0);
      }
      else {
        /** if language is not set returns the array data_widget with locale_dirs and an empty array for result */
        $res[$idx]['data_widget'] = [];
        $res[$idx]['data_widget']['locale_dirs'] = [];
        $res[$idx]['data_widget']['result'] = [];
      }
    }
    $timer->stop('3');
    $success = true;
  }
  /** unset langs and path because don't needed */
  unset( $projects[$i]['langs'], $projects[$i]['path'] );

}

return [
  'time' => $timer->results(),
  'configured_langs' => $configured_langs,
  'primary' => $primaries =$translation->get_primaries_langs(),
  'success' => $success,
  'projects' => $projects,
  'data' => $res
];