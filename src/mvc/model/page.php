<?php
if ( $all = $model->db->get_column_values('bbn_projects', 'id') ){
  //instantiate the class i18n
  $translation = new \bbn\appui\i18n($model->db);

  //return if the user is_dev
  $is_dev =  $model->inc->user->get_session('dev');
  $projects = [];

  foreach ( $all as $id_project ){
    //instantiate the class project
    $project = new \bbn\appui\project($model->db, $id_project);
    $p = [
      'path' => $project->get_path(),
      'langs' => $project->get_langs_id(),
      'id' => $id_project,
      'lang' => $project->get_lang(),
      'name' => $project->get_name()
    ];
    $projects[] = $p;
  }
  //primary languages from bbn_options
  $primaries = $translation->get_primaries_langs();


  //create an array of all languages existing in the table of expressions 'bbn_i18n_exp '
  $langs_in_db = $model->db->get_col_array("SELECT DISTINCT lang FROM bbn_i18n_exp WHERE actif = 1");

  return[
    'langs_in_db' => $langs_in_db,
    'primary' => $primaries,
    'projects' => $projects,
    'is_dev' => $is_dev
  ];
}