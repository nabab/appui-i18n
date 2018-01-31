<?php
if ( $all = $model->db->get_column_values('bbn_projects', 'id') ){
  $id_user = $model->inc->user->get_id();
  $is_admin =  $model->db->val_by_id("bbn_users", "admin", $id_user);
  $projects = [];
  foreach ( $all as $id_project ){

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
  $uid_languages = $model->inc->options->from_code('languages', 'i18n', 'appui');
  $languages = $model->inc->options->full_tree($uid_languages);
  $primaries = array_values(array_filter($languages['items'], function($v) {
    return $v['primary'] == '1';
  }));

  $langs_in_db = $model->db->get_col_array("SELECT DISTINCT lang FROM bbn_i18n_exp WHERE actif = 1");

  return[
    'langs_in_db' => $langs_in_db,
    'primary' => $primaries,
    'projects' => $projects,
    'is_admin' => $is_admin
  ];
}