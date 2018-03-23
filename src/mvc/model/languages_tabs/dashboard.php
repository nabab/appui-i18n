<?php
/*
 * Describe what it does!
 *
 **/

/** @var $this \bbn\mvc\model*/

$timer = new \bbn\util\timer();
/** @var  $projects array of projects*/
$timer->start('1');
$projects = $model->get_model(APPUI_I18N_ROOT.'languages_tabs')['projects'];
$timer->stop('1');
//the first time the dashboard is loaded it returns $res empty and $success null
$res = [];
$success = null;
$translation = new \bbn\appui\i18n($model->db);

foreach ( $projects as $i => $p ){
  //when an id_project is sent by the post of dashboard, the first time at mounted, then at every @change of the
  // projects dropdown $res is filled with the widgets relative to this id_project and success = true
  //takes the languages configured in db for the project
  if ( $model->data['id_project'] === $projects[$i]['id'] ){
    $timer->start('2');
    $project = $projects[$i];
    $project_class = new \bbn\appui\project($model->db, $projects[$i]['id']);
    $configured_langs = [];
    //takes the langs configured in db for the project translation
    foreach ( $project_class->get_langs() as $p ){
      $configured_langs[] = $p['id'];
    }
    $timer->stop('2');
    $timer->start('3');
    foreach ( $project['path'] as $idx => $pa ){
      //takes the full option of each path
      $res[$idx] = $model->inc->options->option($projects[$i]['path'][$idx]['id_option']);
      //if the property language is already set for the path takes the cached model of the option
      if ( isset( $res[$idx]['language'] ) ){
        $res[$idx]['data_widget'] = $model->get_cached_model(APPUI_I18N_ROOT.'languages_tabs/data/widgets', ['id_option' => $res[$idx]['id']], 0);
        }
    }
    $timer->stop('3');
    $success = true;
  }
  unset($projects[$i]['langs'], $projects[$i]['path']);
}

return [
  'time' => $timer->results(),
  'configured_langs' => $configured_langs,
  'primary' => $primaries =$translation->get_primaries_langs(),
  'success' => $success,
  'projects' => $projects,
  'data' => $res
];