<?php

//$root the option projects
if ( $root = $model->inc->options->fromCode('list', 'project', 'appui') ){
  //all projects x
  $projects_ids = $model->inc->options->items($root);
  //instantiate the class i18n
  $translation = new \bbn\Appui\I18n($model->db);
  //primary languages from bbn_options
  $primaries = $translation->getPrimariesLangs();
  //return if the user is_dev
  $is_dev =  $model->inc->user->getSession('dev');
  $projects = [];
  //creates the array projects
  foreach ( $projects_ids as $p ){
    $project = new \bbn\Appui\Project($model->db, $p);
    // if the option lang of the project is empty it creates the options using primaries langs
    $projects[] = $project->getProjectInfo();
  }

  //adds the fake project options
  /** path will be filled only if the project 'options' is selected by the dropdown of the dashboard*/

  $projects[] = [
    'path' => [],
    'langs' => array_map(function($p){
      return $p['id'];
    }, $primaries),
    'id' => 'options',
    'lang' => 'en',
    'name' => _('Options')
  ];



  //create an array of all languages existing in the table of expressions 'bbn_i18n_exp '
  $langs_in_db = $model->db->getColArray("SELECT DISTINCT lang FROM bbn_i18n_exp");

  return[
    'langs_in_db' => $langs_in_db,
    'primary' => $primaries,
    'projects' => $projects,
    'is_dev' => $is_dev
  ];
}