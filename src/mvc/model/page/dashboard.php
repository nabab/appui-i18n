<?php
/*
 * Describe what it does!
 *
 **/

/** @var $this \bbn\mvc\model*/
/** @var  $projects array of projects*/

$projects = [];
//$id_project = $model->data['id_project'] ?? $model->inc->options->from_code('apst-app', 'projects', 'appui');
if ( $opt_projects = $model->inc->options->from_code('projects', 'appui') ){
  $ids = array_keys($model->inc->options->options($opt_projects));
  if ( !empty($ids) ){
    foreach($ids as $i => $id){
      $project = new \bbn\appui\project($model->db, $id);
      $opt = $model->inc->options;
      $projects[$i] = $project->get_project_info();
    }
  }
}
$project_idx;

if ( !empty($model->data['id_project']) ){
  $project_idx = \bbn\x::find($projects, ['id' => $model->data['id_project']]);
}
else{
  $project_idx = 0;
}


//the first time the dashboard is loaded it returns $res empty and $success null
$res = [];
$success = null;

$uid_languages =  $model->inc->options->from_code('languages', 'i18n', 'appui');
$primaries = [];
if ( $languages = $model->inc->options->full_tree($uid_languages) ){
  $filter = array_filter($languages['items'], function($v) {
    return !empty($v['primary']);
  });
  $primaries = array_values($filter);
}

if ( isset($project_idx) && ($current_project = $projects[$project_idx]) && isset($projects[$project_idx]['path'])){
  $translation = new \bbn\appui\i18n($model->db, $current_project['id']);
  foreach ( $current_project['path'] as $idx => $pa ){

    /** for every project takes the full option of each path */
    if ( $res_idx = $model->inc->options->option($current_project['path'][$idx]['id_option'])){
      $res[$idx] = $res_idx;
    }
    /** if language is set takes the cached_model_of the widget */
    if ( isset( $res[$idx]['language'] ) ){
      //the id_option of the widget
      $id_option = $res[$idx]['id'];
      
      //if the widget has not cache for this method creates the cache
      //IF THE CACHE IS ACTIVE WHEN THE PROJECT IS CHANGED BY THE DROPDOWN IT RETURNS THE WIDGETS OF THE PROJECT APST-APP
      /*if ( empty($translation->cache_has($id_option, 'get_translations_widget')) ){
        //set data in cache $translation->cache_set($id_option, (string)method name, (array)data)
        $translation->cache_set($id_option, 'get_translations_widget',
          $translation->get_translations_widget($current_project['id'], $id_option)
        );
      }
      $res[$idx]['data_widget'] = $translation->cache_get($id_option, 'get_translations_widget');*/
      $res[$idx]['data_widget'] = $translation->get_translations_widget($current_project['id'], $id_option);
    }
    else {
      /** if language is not set returns the array data_widget with locale_dirs and an empty array for result */
      $res[$idx]['data_widget'] = [];
      $res[$idx]['data_widget']['locale_dirs'] = [];
      $res[$idx]['data_widget']['result'] = [];
    }
    $res[$idx]['title'] = $model->inc->options->text($res[$idx]['id_parent']).'/'.$res[$idx]['text'];
  }
  $success = true;
}

/** @var  $translation instantiate the class i18n */
foreach ( $projects as $i => $p ){
  /** unset langs and path because don't needed */
  unset( $projects[$i]['path'] );
}

return [
  'configured_langs' => $projects[$project_idx]['langs'],
  'primary' => $primaries,
  'success' => $success,
  'projects' => $projects,
  'data' => $res
];
