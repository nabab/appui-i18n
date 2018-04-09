<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 08/01/18
 * Time: 13.25
 */


$success = false;
// row sent by post
if ( !empty($model->data['row']['id_exp'])){
  $row = $model->data['row'];
  //receives configured langs from the post
  $langs = $model->data['langs'];
  foreach( $langs as $l ){

    if ( !empty($row[$l]) ){

      $expression = $row[$l];

      //case update the expression exists in this lang ($l)
      if ( $id = $model->db->get_val('bbn_i18n_exp', 'id', [
        'id_exp' => $row['id_exp'],
        'lang' => $l
      ]) ){

        if ( $model->db->update('bbn_i18n_exp', ['expression' => $expression, 'actif' => 1 ], [
          'id' => $id,

        ]) ){
          $success = true;
        }
      }

      //case insert
      else {
        $success = $model->db->insert_ignore('bbn_i18n_exp', [
          'expression' => $expression,
          'id_exp' => $row['id_exp'],
          'lang' => $l,
          'actif' => 1
        ]);
      }
    }
  }
  $model->get_cached_model(APPUI_I18N_ROOT.'actions/find_strings', ['id_option'=> $model->data['id_option']], true);
	$model->get_cached_model(APPUI_I18N_ROOT.'page/data/widgets', ['id_option'=> $model->data['id_option']], true);
  $model->get_cached_model(APPUI_I18N_ROOT.'page/data/strings_table', ['id_option' => $ctrl->data['id_option']], true);
  $model->get_model(APPUI_I18N_ROOT.'actions/generate', ['id_option' => $model->data['id_option']]);
}
return [
  'row' => $model->data['row'],
  'success' => $success
];