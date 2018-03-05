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
  //langs for which this project has been configured
  $langs = $project->get_langs();


  return [
    'empty_row_deleted' => $deleted_empty,
    'pageTitle' =>  $model->inc->options->text($model->data['id_option']),
    'source_lang' => $lang,
    'langs' => $project->get_langs(),
    'success' => true
  ];
}
