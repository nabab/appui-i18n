<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 14/12/17
 * Time: 13.05
 */
/*

if (
  isset($model->data['id_option']) &&
  ($o = $model->inc->options->option($model->data['id_option']))
){

  $model->data['success'] = false;
  if (
    ($parent = $model->inc->options->parent($o['id'])) &&
    defined($parent['code'])
  ){
    $to_explore = constant($parent['code']).$o['code'];
    $files = \bbn\file\dir::scan($to_explore, 'php');
    $translations = new \Gettext\Translations();
    $todo = [];
    foreach ( $files as $f ){
      if ( $tmp = \Gettext\Translations::fromPhpCodeFile($f, ['functions' => ['_' => 'gettext']]) ){
        $translations->mergeWith($tmp);
      }
    }
    foreach ( $translations->getIterator() as $r => $tr ){
      $todo[] = $tr->getOriginal();
      $model->data['success'] = true;
    }

    //basing on the path id_option I take from the db the languages for which this project is configured to use it in the table of strings
    if( !empty($id_project = $model->db->select_one('bbn_projects_assets', 'id_project', ['id_option' => $model->data['id_option']]) ) ){
      $asset_type_lang = $model->inc->options->from_code('lang', 'assets','projects','appui');
      $langs = $model->db->get_field_values('bbn_projects_assets', 'id_option', [
        'id_project' => $id_project,
        'asset_type' => $asset_type_lang
      ]);
    };


    return [
      'langs' => $langs,
      'files' => $files,
      'path' => $to_explore,
      'todo' => $todo,
      'success' => $model->data['success'],
    ];
  }
}

*/
if( !empty($model->data['id_option'] )){
  $id_parent = $model->inc->options->get_id_parent($model->data['id_option']);
  //$source_lang
  die(var_dump($id_parent, $model->data['id_option'], get_class_methods($model->inc->options)));
}