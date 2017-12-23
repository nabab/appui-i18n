<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 12/12/17
 * Time: 15.13
 */


if (
  isset($model->data['id_option']) &&
  ($o = $model->inc->options->option($model->data['id_option']))
){
  $asset_type_path =  $model->inc->options->from_code('path', 'assets','projects','appui');

  $id_project = $model->db->get_val(
    'bbn_projects_assets',
    'id_project',
    [
      'id_option' => $model->data['id_option'],
      'asset_type' => $asset_type_path
    ]
  );

  $project = new \bbn\appui\project($model->db, $id_project);
  $project_lang = $project->get_lang();

  $langs = $project->get_langs();

  $model->data['success'] = false;

  if (
    ($parent = $model->inc->options->parent($o['id'])) &&
    defined($parent['code'])
  ){
    $to_explore = constant($parent['code']).$o['code'];
    $files = [];
    $i18n = new \bbn\appui\i18n($model->db);
    $i18n->analyse_folder($to_explore, true);
    $todo = $i18n->result();
    $done = 0;

    foreach ( $todo as $t ){
      if ( !($id = $model->db->select_one('bbn_i18n', 'id', ['exp' => $t])) ){
       $model->db->insert('bbn_i18n', [
          'exp' => $t,
          'last_modified' => date('Y-m-d H:i:s'),
          'id_user' => $model->inc->user->get_id(),
          'lang' => $project_lang,
        ]);
        $id = $model->db->last_id();
      }

      if( !( $id_exp = $model->db->select_one('bbn_i18n_exp', 'id_exp', [ 'id_exp' => $id, 'lang' => $project_lang ]) ) ){
        $done += (int)$model->db->insert_ignore('bbn_i18n_exp', [
          'id_exp' => $id,
          'lang' => $project_lang,
          'expression' => $t
        ]);
        if( !empty($done) ){
          $model->data['success'] = true;
        }
        else{
          $model->data['success'] = true;
          var_dump('No strings to update');
        }
      }
    }

    return [
      'total' => count($todo),
      'done' => $done,
      'langs' => $langs,
      'files' => $files,
      'path' => $to_explore,
      'todo' => $todo,
      'success' => $model->data['success'],
    ];
  }
}