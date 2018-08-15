<?php

if ( $all = $model->db->get_column_values('bbn_projects', 'id') ){
  //instantiate the class i18n
  $translation = new \bbn\appui\i18n($model->db);

  //return if the user is_dev
  $is_dev =  $model->inc->user->get_session('dev');
  $projects = [];

  foreach ( $all as $id_project ){
    //instantiate the class project
    $projects[] = $model->get_model('internationalization/page/data/project', ['id_project' => $id_project]);
  }

  //primary languages from bbn_options
  $primaries = $translation->get_primaries_langs();

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
  $langs_in_db = $model->db->get_col_array("SELECT DISTINCT lang FROM bbn_i18n_exp WHERE bbn_h = 1");

  return[
    'langs_in_db' => $langs_in_db,
    'primary' => $primaries,
    'projects' => $projects,
    'is_dev' => $is_dev
  ];
}