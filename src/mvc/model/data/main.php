<?php
use bbn\Appui\I18n;
use bbn\Appui\Project;
use bbn\Str;
use bbn\X;

if ($root = $model->inc->options->fromCode('list', 'project', 'appui')) {
  $projectsIds = $model->inc->options->items($root) ?: [];
  $projects = [];
  $projectId = null;
  if ($model->hasData('project', true)) {
    if ($model->data['project'] === 'options') {
      $projectId = 'options';
    }
    else if (!Str::isUid($model->data['project'])) {
      $projectId = $model->inc->options->fromCode($model->data['project'], 'list', 'project', 'appui');
    }
    else {
      $projectId = $model->data['project'];
    }

    if (!empty($projectId)) {
      if (!in_array($projectId, $projectsIds)) {
        throw new Exception(X::_("The project '%s' is not valid.", $projectId));
      }

      $project = new Project($model->db, $projectId);
      $projects[] = $project->getProjectInfo();
    }
  }

  $translation = new I18n($model->db, $projectId);
  $primaries = $translation->getPrimariesLangs();
  if (empty($projectId)) {
    foreach ($projectsIds as $p ){
      $project = new Project($model->db, $p);
      $projects[] = $project->getProjectInfo();
    }

    //adds the fake project options
    /** path will be filled only if the project 'options' is selected by the dropdown of the dashboard*/
    $projects[] = [
      'id' => 'options',
      'code' => 'options',
      'name' => _('Options'),
      'path' => [],
      'langs' => array_map(function($p){
        return $p['id'];
      }, $primaries),
      'lang' => 'en'
    ];
  }

  return [
    'success' => true,
    'data' => [
      //create an array of all languages existing in the table of expressions 'bbn_i18n_exp '
      'langs_in_db' => $model->db->getColArray("SELECT DISTINCT lang FROM bbn_i18n_exp"),
      'primary' => $primaries,
      'projects' => $projects,
      'id_project' => $projectId,
    ]
  ];
}

return [
  'success' => false
];
