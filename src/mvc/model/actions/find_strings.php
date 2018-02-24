<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 12/12/17
 * Time: 15.13
 */


/** @todo this $model should set the cached model, I need help to do it. Now the cached model is created when opening
 * strings tab*/
//called when the button to search for new string in a path is clicked and from internationalization/languages/strings_tab/ to open the tab of the strings in the path

if (
  isset($model->data['id_option']) &&
  ($o = $model->inc->options->option($model->data['id_option']))
){
  $asset_type_path = $model->inc->options->from_code('path', 'assets','projects','appui');

  //get the id of the current project using the id_option of the path
  $id_project = $model->db->get_val(
    'bbn_projects_assets',
    'id_project',
    [
      'id_option' => $model->data['id_option'],
      'asset_type' => $asset_type_path
    ]
  );
  //instantiate an object to the class appui\project
  $project = new \bbn\appui\project($model->db, $id_project);

  //$project_lang  the source language of the project
  $project_lang = $project->get_lang();

  //$langs for which the project is configured
  $langs = $project->get_langs();
  if (
    ($parent = $model->inc->options->parent($o['id'])) &&
    defined($parent['code'])
  ){
    //$to_explore is the directory to explore for strings
    $to_explore = constant($parent['code']).$o['code'];

    //instantiate $i18n to the class appui\i18n
    $i18n = new \bbn\appui\i18n($model->db);

    // $res array of the files indexed to the strings found in the directory $to_explore
    $res = $i18n->analyse_folder($to_explore, true);

    $done = 0;

    //$r is the string, $val is the array of files in which this string is contained
    foreach ( $res as $r => $val ){

      //for each string create a property 'path' containing the files' name in which the string is contained

      $res[$r] = ['path' => $val];

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
      $res[$r]['id_exp'] = $id;
      //put the string $r into the property 'original_exp' (I'll use only array_values at the end)
      $res[$r]['original_exp'] = $r;

      //check in 'bbn_i18n_exp' table of db if the string $r exist for this $project_lang
      if( !( $id_exp = $model->db->select_one('bbn_i18n_exp', 'id_exp', [
        'id_exp' => $id,
        'lang' => $project_lang
      ]) ) ){
        //if the string $r is not in 'bbn_i18n_exp' inserts the string
        //$done will be the number of strings found in the folder $to_explore that haven't been found in the table
        // 'bbn_i18n_exp' of db, so $done is the number of new strings inserted in in 'bbn_i18n_exp'
        $done += (int)$model->db->insert('bbn_i18n_exp', [
          'id_exp' => $id,
          'lang' => $project_lang,
          'expression' => $r
        ]);
      }
      //$langs the array of languages for which the project is configured using the form
      foreach ( $langs as $lng ){
        //create a property indexed to the code of $lng containing the string $r from 'bbn_i18n_exp' in this $lng
        $res[$r][$lng['code']] = (string)$model->db->select_one(
          'bbn_i18n_exp',
            'expression',
          [
            'id_exp' => $id,
            'lang' => $lng['code']
          ]
        );
      }
    }
    //set a cached model for this id_option


//    $model->get_cached_model('internationalization/actions/find_strings'.$model->data['id_option']);

    $model->data['success'] = true;

    $ret = [
      'res' => array_values($res),
      'done' => $done,
      'langs' => $langs,
      'path' => $to_explore,
      'success' => $model->data['success']
    ];
    $model->set_cache($ret, $model->data['id_option'], 0);
    return $ret;
  }
}