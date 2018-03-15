<?php
/*
 * Describe what it does!
 *
 **/

/** @var $this \bbn\mvc\model*/

/** @var  $projects array of projects*/
$projects = $model->get_model(APPUI_I18N_ROOT.'languages_tabs')['projects'];
//the first time the dashboard is loaded it returns $res empty and $success null
$res = [];
$success = null;
$translation = new \bbn\appui\i18n($model->db);

foreach ( $projects as $i => $p ){
  //when an id_project is sent by the post of dashboard, the first time at mounted, then at every @change of the
  // projects dropdown $res is filled with the widgets relative to this id_project and success = true
  //takes the languages configured in db for the project
 if ( $model->data['id_project'] === $projects[$i]['id'] ){

   $project = $projects[$i];
   $project_class = new \bbn\appui\project($model->db, $projects[$i]['id']);
    $configured_langs = $project_class->get_langs();
    foreach ( $project['path'] as $idx => $pa ){
      $res[$idx] = [
        'title' => $projects[$i]['name']. '/' . $projects[$i]['path'][$idx]['text'],
        'key' => $projects[$i]['path'][$idx]['id_option'],
        'component' => 'appui-languages-widget',
        'url' => APPUI_I18N_ROOT.'languages_tabs/data/widgets/'.$projects[$i]['path'][$idx]['id_option'],
        'id_project' => $project['id'],
        'buttonsRight' => [[
          'text' => 'Check for new strings in files and new translations',
          'icon' => 'fa fa-flash',
          'action' => 'find_strings'
          ],[
          'text' => 'Configure locale folder of translation\'s files for this path',
          'icon' => 'fa fa-flag',
          'action' => 'config_locale_dir'
        ],[
          'text' => 'Open the strings table of this path',
          'icon' => 'fa fa-book',
          'action' => 'open_strings_table'
        ]
        ]
      ];
    }
    $success = true;
  }

  unset($projects[$i]['langs'], $projects[$i]['path']);
}

return [
  'configured_langs' => $configured_langs,
  'primary' => $primaries =$translation->get_primaries_langs(),
  'success' => $success,
  'projects' => $projects,
  'data' => $res
];