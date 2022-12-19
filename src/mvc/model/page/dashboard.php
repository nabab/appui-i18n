<?php
/*
 * Describe what it does!
 *
 **/

/** @var $model \bbn\Mvc\Model*/

Use bbn\X;

/** @var  $projects array of projects*/
$projects = [];
$primaries = [];
if ($projectsIds = $model->inc->options->items('list', 'project', 'appui')) {
  foreach ($projectsIds as $id) {
    $projectCls = new \bbn\Appui\Project($model->db, $id);
    $projectInfo = $projectCls->getProjectInfo();
    /** unset path because don't needed */
    unset($projectInfo['path']);
    $projects[] = $projectInfo;
    if (empty($primaries)) {
      $primaries = $projectCls->getPrimariesLangs();
    }
  }
}
$projects[] = [
  'langs' => array_map(function($p){
    return $p['id'];
  }, $primaries),
  'id' => 'options',
  'lang' => 'en',
  'name' => _('Options')
];
$dash = $model->getModel($model->pluginUrl(). '/data/dashboard', ['idProject' => $projects[0]['id']]);
return $model->addData([
  'configured_langs' => $projects[0]['langs'],
  'primary' => $primaries,
  'success' => $dash['success'],
  'projects' => $projects,
  'data' => $dash['paths']
])->data;
