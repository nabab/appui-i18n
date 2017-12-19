<?php
if ( $all = $model->db->get_column_values('bbn_projects', 'id') ){

  $projects = [];
  foreach ( $all as $id_project ){

    $project = new \bbn\appui\project($model->db, $id_project);
    $p = [
      'path' => $project->get_path(),
      'langs' => $project->get_langs($p['id'], $asset_type_lang),
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

  return[
    'primary' => $primaries,
    'projects' => $projects
  ];
}
