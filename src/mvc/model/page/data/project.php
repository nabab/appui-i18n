<?php
/*
 * Describe what it does!
 *
 **/

/** @var $this \bbn\mvc\model*/
if ( !empty($model->data['id_project']) ){
  $project = new \bbn\appui\project($model->db, $model->data['id_project']);
  if ( $project->check() ){
  	return [
      'path' => $project->get_path(),
      'langs' => $project->get_langs_id(),
      'id' => $model->data['id_project'],
      'lang' => $project->get_lang(),
      'name' => $project->get_name()
    ];
  }
  return [];
}