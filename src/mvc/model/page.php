<?php

//$root the option projects
if ( $root = $model->inc->options->from_code('projects', 'appui') ){
  //all projects x
  $tmp = $model->inc->options->options($root);
  // removing the option assets (that still exists) from array
  
  //array of projects ids
  $projects_ids = array_keys($tmp);
  //instantiate the class i18n
  $translation = new \bbn\appui\i18n($model->db);
  //primary languages from bbn_options
  $primaries = $translation->get_primaries_langs();
  //return if the user is_dev
  $is_dev =  $model->inc->user->get_session('dev');
  $projects = [];
  
  
  //creates the array projects
  foreach ( $projects_ids as $p ){
    $project = new \bbn\appui\project($model->db, $p);
    // if the option lang of the project is empty it creates the options using primaries langs
    
    $projects[] = $project->get_project_info();
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
  $langs_in_db = $model->db->get_col_array("SELECT DISTINCT lang FROM bbn_i18n_exp");

  return[
    'langs_in_db' => $langs_in_db,
    'primary' => $primaries,
    'projects' => $projects,
    'is_dev' => $is_dev
  ];
}