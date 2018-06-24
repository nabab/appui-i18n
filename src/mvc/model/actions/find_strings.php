<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 12/12/17
 * Time: 15.13
 */


/** @todo this $model should set the cached model, I need help to do it. Now the cached model is created when opening
 * strings tab*/
//called when the button to search for new string in a path is clicked and from
// internationalization/page/path_translations/ to open the tab of the strings in the path


if ( empty($model->data['language']) ){
  $model->data['language'] = $model->inc->options->get_prop($model->data['id_option'], 'language');
}

if (
  ($id_option = $model->data['id_option']) &&
  ($o = $model->inc->options->option($id_option)) &&
  !empty($o['language']) &&
  ($parent = $model->inc->options->parent($id_option)) &&
  defined($parent['code']) &&
  ($source_language = $model->data['language'])
){
  /**instantiate $i18n to the class appui\i18n*/
  $i18n = new \bbn\appui\i18n($model->db);
  /** @var  $to_explore the path to explore*/
  $to_explore = constant($parent['code']).$o['code'];

  /** @var  $locale_dir  the root of locale dir for this id_option*/
  $locale_dir = dirname($to_explore).'/locale';
  /** @var $dirs scans dirs contained in locale folder of this path*/
  $dirs = scandir($locale_dir, 1);

  /** @var (array) $languages based on locale dirs found in the path*/
  $languages = array_map(function($a){
    return basename($a);
  }, \bbn\file\dir::get_dirs($locale_dir)) ?: [];

  /** $res array of the files found in the directory $to_explore */
  $res = $i18n->analyze_folder($to_explore, true);

  $done = 0;

  foreach ( $res as $r => $val ){

    /**  for each string create a property 'path' containing the files' name in which the string is contained */

    $res[$r] = ['path' => $val];

    /**  checks if the table bbn_i18n of db already contains the string $r for this $source_lang */
    if ( !($id = $model->db->select_one('bbn_i18n', 'id', [
      'exp' => $r,
      'lang' => $source_language
    ])) ){
      /** if the string $r is not in 'bbn_i18n' inserts the string */
      $model->db->insert('bbn_i18n', [
        'exp' => $r,
        //'last_modified' => date('Y-m-d H:i:s'),
        //'id_user' => $model->inc->user->get_id(),
        'lang' => $source_language,
      ]);
      $id = $model->db->last_id();
    }
    /** create the property 'id_exp' for the string $r */
    $res[$r]['id_exp'] = $id;

    /** puts the string $r into the property 'original_exp' (I'll use only array_values at the end) */
    $res[$r]['original_exp'] = $r;

    /** checks in 'bbn_i18n_exp' if the string $r already exist for this $source_lang */
    if( !( $id_exp = $model->db->select_one('bbn_i18n_exp', 'id_exp', [
      'id_exp' => $id,
      'lang' => $source_language
    ]) ) ){

      /**if the string $r is not in 'bbn_i18n_exp' inserts the string
        $done will be the number of strings found in the folder $to_explore that haven't been found in the table
       'bbn_i18n_exp' of db, so $done is the number of new strings inserted in in 'bbn_i18n_exp'*/
      $done += (int)$model->db->insert('bbn_i18n_exp', [
        'id_exp' => $id,
        'lang' => $source_language,
        'expression' => $r
      ]);
    }
    /** $languages is the array of languages existing in locale dir*/
    foreach ( $languages as $lng ){
      /**  create a property indexed to the code of $lng containing the string $r from 'bbn_i18n_exp' in this $lng */
      $res[$r][$lng] = (string)$model->db->select_one(
        'bbn_i18n_exp',
          'expression',
        [
          'id_exp' => $id,
          'lang' => $lng
        ]
      );
    }
  }

  $model->data['success'] = true;

  return [
    'id_option' => $id_option,
    'res' => array_values($res),
    'done' => $done,
    'languages' => $languages,
    'path' => $to_explore,
    'success' => $model->data['success']
  ];
}