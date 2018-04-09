<?php
/*
 * Describe what it does!
 *
 **/

/** @var $this \bbn\mvc\model*/

$timer = new \bbn\util\timer();
/** @var  $projects array of projects*/
$timer->start('1');
$projects = $model->get_model(APPUI_I18N_ROOT.'page')['projects'];
$timer->stop('1');
//the first time the dashboard is loaded it returns $res empty and $success null
$res = [];
$success = null;
$translation = new \bbn\appui\i18n($model->db);

foreach ( $projects as $i => $p ){

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
      //if the property language is already set for the path takes the cached model of the option else return an empty object
      if ( isset( $res[$idx]['language'] ) ){
        //takes the model of the widget from cache if it exists else creates one
        $res[$idx]['data_widget'] = $model->get_cached_model(APPUI_I18N_ROOT.'page/data/widgets', ['id_option' => $res[$idx]['id']], 0);
      }
      else {
        $res[$idx]['data_widget'] = [];
        //locale dirs is the list of dirs found in locale for option with the property language defined
        $res[$idx]['data_widget']['locale_dirs'] = [];
      }
    }
    $timer->stop('3');
    $success = true;
  }
  //unset langs and path because don't needed
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