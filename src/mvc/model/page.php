<?php

//$root the option projects
if (defined('BBN_APP_NAME')
  && ($root = $model->inc->options->fromCode('list', 'project', 'appui'))
) {
  //all projects
  $tmp = $model->inc->options->options($root);
  //array of projects ids
  $projectsIds = !empty($tmp) ? array_keys($tmp) : [];
  //instantiate the class i18n
  $translation = new \bbn\Appui\I18n($model->db, BBN_APP_NAME);
  //primary languages from bbn_options
  $primaries = $translation->getPrimariesLangs();
  // Projects list
  $projects = [];
  //creates the array projects
  foreach ($projectsIds as $p ){
    $project = new \bbn\Appui\Project($model->db, $p);
    // if the option lang of the project is empty it creates the options using primaries langs

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

  return [
    //create an array of all languages existing in the table of expressions 'bbn_i18n_exp '
    'langs_in_db' => $model->db->getColArray("SELECT DISTINCT lang FROM bbn_i18n_exp"),
    'primary' => $primaries,
    'projects' => $projects,
    'is_dev' => $model->inc->user->getSession('dev')
  ];
}
