<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 18/01/18
 * Time: 16.57
 */
//this script check all paths of the projects for strings
/*
if (!empty( $id_project = $model->data['project']) ){
  $model->data['success'] = false;

  $project = new \bbn\appui\project($model->db, $id_project);


  $i18n = new \bbn\appui\i18n($model->db);

  $id_options = $project->get_path();

  $paths = [];
  $to_explore = [];
  $full = [];
  foreach ($id_options as $i => $val){
     $parent = $model->inc->options->parent($val['id_option']);
     $paths[] = $val['code'];
    foreach( $paths as $p ){
      $to_explore[] = constant($parent['code']).$p;
    }
  }
  foreach( $to_explore as $t ){
    $i18n->analyse_folder($t, true);

  }
  $todo[] = $i18n->result();
  foreach ($todo as $t ){
    $model->db->insert('test_i18n', ['exp' => $t, 'lang' => 'en']);
  }

}*/
