<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 07/03/18
 * Time: 10.37
 */

$projects = $model->get_model(APPUI_I18N_ROOT.'languages_tabs')['projects'];

if ( !empty( $id_option = $model->data['id_option']) &&
  ($o = $model->inc->options->option($id_option)) &&
  ($parent = $model->inc->options->parent($id_option)) &&
  defined($parent['code']) ){
  $to_explore = constant($parent['code']).$o['code'];
  $locale_dir = dirname($to_explore).'/locale';


  $languages = array_map(function($a){
    return basename($a);
  }, \bbn\file\dir::get_dirs($locale_dir)) ?: [];

  $translation = new \bbn\appui\i18n($model->db);
  $expressions = $translation->analyze_folder($to_explore, true);

  $new = 0;
  //get the id of the project from id_option
  $id_project = $translation->get_id_project($id_option, $projects);


  //instantiate the class appui\project
  $project = new \bbn\appui\project($model->db, $id_project);
  /**@var (string) the source lang of the project*/
  $project_lang = $project->get_lang();

  //$r is the string, $val is the array of files in which this string is contained
  $i = 0;
  $res = [];
  foreach ( $expressions as $r => $val ){
    //for each string create a property 'path' containing the files' name in which the string is contained

    $res[$i] = ['path' => $val];

    //check if the table bbn_i18n of db already contains the string $r for this $project_lang

    if ( !($id = $model->db->select_one('bbn_i18n', 'id', [
      'exp' => $r,
      'lang' => $project_lang
    ])) ){

      //if the string $r is not in 'bbn_i18n' inserts the string
      $model->db->insert('bbn_i18n', [
        'exp' => $r,
        'last_modified' => date('Y-m-d H:i:s'),
        'id_user' => $model->inc->user->get_id(),
        'lang' => $project_lang,
      ]);
      $id = $model->db->last_id();
    }
    //create the property 'id_exp' for the string $r
    $res[$i]['id_exp'] = $id;

    //put the string $r into the property 'original_exp' (I'll use only array_values at the end)
    $res[$i]['original_exp'] = $r;

    //check in 'bbn_i18n_exp' table of db if the string $r exist for this $project_lang
    if( !( $id_exp = $model->db->select_one('bbn_i18n_exp', 'id_exp', [
      'id_exp' => $id,
      'lang' => $project_lang
    ]) ) ){
      //if the string $r is not in 'bbn_i18n_exp' inserts the string
      //$new will be the number of strings found in the folder $to_explore that has not been found in the table
      // 'bbn_i18n_exp' of db, so $new is the number of new strings inserted in in 'bbn_i18n_exp'
      $new += (int)$model->db->insert_ignore('bbn_i18n_exp', [
        'id_exp' => $id,
        'lang' => $project_lang,
        'expression' => $r
      ]);
    }

    /**var (array) the languages found in locale dir*/
    if ( !empty($languages) ){
      foreach ( $languages as $lng ){
        //create a property indexed to the code of $lng containing the string $r from 'bbn_i18n_exp' in this $lng

        $res[$i]['translation'][$lng] = (string)$model->db->select_one(
          'bbn_i18n_exp',
          'expression',
          [
            'id_exp' => $id,
            'lang' => $lng
          ]
        );
      }
    }

    $i++;
    $success = true;
  }
  return [
    'source_lang' => $project_lang,
    'path' => $project->get_path_text($model->data['id_option']),
    'success' => $success,
    'new' => $new,
    'languages' => $languages,
    'total' => count($res),
    'res' => $res,
    'id_option' => $model->data['id_option']
  ];
}