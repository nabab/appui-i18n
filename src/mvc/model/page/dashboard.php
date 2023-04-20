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
  'id' => 'options',
  'code' => 'options',
  'name' => _('Options'),
  'langs' => array_map(function($p){
    return $p['id'];
  }, $primaries),
  'lang' => 'en'
];
$currentProj = $projects[0];
if (\defined('BBN_APP_NAME')
  && ($p = X::getRow($projects, ['code' => BBN_APP_NAME]))
) {
  $currentProj = $p;
}
$dash = $model->getModel($model->pluginUrl(). '/data/dashboard', ['idProject' => $currentProj['id']]);
return $model->addData([
  'configured_langs' => $currentProj['langs'],
  'primary' => $primaries,
  'success' => $dash['success'],
  'projects' => $projects,
  'data' => $dash['paths']
])->data;
