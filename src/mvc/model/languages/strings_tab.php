<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 14/12/17
 * Time: 13.05
 */

/* @var string ID of the path to analyze is expected */

if ( !empty($model->data['id_option']) ){

  //delete from db rows having an empty string as expression
  $deleted_empty = $model->db->delete('bbn_i18n_exp', ['expression' => '']);

  /** @var array Root of the path */
  $parent = $model->inc->options->parent($model->data['id_option']);
  // Parent's code must correspond to a defined constant
  if ( defined($parent['code']) && ($path = $model->inc->options->code($model->data['id_option'])) ){
    // The constant contains a path
    $to_explore = constant($parent['code']).$path;
    /** @var \bbn\appui\i18n Language object */
    $i18n = new \bbn\appui\i18n($model->db);
    // Analyzing the path
    $i18n->analyse_folder($to_explore, true);
    $todo = $i18n->result();

    /** @var string The project's ID */
    $id_project = $model->db->select_one('bbn_projects_assets', 'id_project', ['id_option' => $model->data['id_option']]);
    /** @todo Problem: a same source can be used by multiple projects ie configs
    		Think about either:
        - making a source exclusive to a project
        - add the project's ID
        - give directly the language to the source
    */
    /** @var \bbn\appui\project The project object */
    $project = new \bbn\appui\project($model->db, $id_project);
    /** @var string Language code in which the project is written */
    $lang = $project->get_lang();
    $langs = $project->get_langs();

    $source_glossary = [];


    $id_user = $model->inc->user->get_id();

    // $todo is the result of the search for strings in the path
    foreach( $todo as $t ){

      //check if the string is present in db for the source_lang
      $db_str = $model->db->rselect('bbn_i18n', [], [
        'lang' => $lang,
        'exp' => $t
      ]);

      //if the string doesn't exist in db insert the string new string
      if ( empty($db_str) ){
        $data = [
          'exp' => $t,
          'last_modified' => date('Y-m-d H:i:s'),
          'id_user' => $id_user,
          'lang' => $lang
        ];

        if ( $model->db->insert('bbn_i18n', $data) ){
          $data['id'] = $model->db->last_id();
          $tmp = [
            'id_exp' => $data['id'],
            'lang' => $lang,
            'expression' => $t
          ];

          if ( $model->db->insert('bbn_i18n_exp', $tmp) ){
            $db_str = $data;
          }
        }
      }
      if ( !empty($db_str) ){
        $tmp = $db_str;
        foreach ( $langs as $lng ){
          $tmp[$lng['code']] = (string)$model->db->select_one('bbn_i18n_exp', 'expression', [
            'id_exp' => $tmp['id'],
            'lang' => $lng['code']
          ]);
        }
        $source_glossary[] = $tmp;
      }
    }

    return [
      'empty_row_deleted' => $deleted_empty,
      'source_glossary' => $source_glossary,
      'pageTitle' => $path.'\'s translations',
      'source_lang' => $lang,
      'this_path' => $model->inc->options->text($model->data['id_option']),
      'langs' => $project->get_langs()
    ];
  }
}