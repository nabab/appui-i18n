<?php
/*
 * Describe what it does!
 *
 **/
/** @var $this \bbn\Mvc\Model*/
if ( !empty($model->data['id_project']) ){
  $project = new \bbn\Appui\Project($model->db, $model->data['id_project']);
  if ( $project->check() ){
   	return [
      'path' => $project->getPath(),
      'langs' => $project->getLangsIds(),
      'id' => $model->data['id_project'],
      'lang' => $project->getLang(),
      'name' => $project->getName()
    ];
  }
  return [];
}