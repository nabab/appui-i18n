<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 08/01/18
 * Time: 13.25
 */


$success = false;
/** at @change the table send row */
if ( !empty($model->data['row']['id_exp'])){
  /** @var $row the row sent by strings table */
  $row = $model->data['row'];
  /** @var (array) $langs sent by strings table*/
  $langs = $model->data['langs'];
  $deleted = false;
  foreach( $langs as $l ){
    if ( !empty($row[$l]) ){
      /** @var $expression the string */
      $expression = $row[$l];
      /** @var $id if the $id of the string exists */

      if ( $id = $model->db->get_val('bbn_i18n_exp', 'id', [
        'id_exp' => $row['id_exp'],
        'lang' => $l
      ]) ){

        /** UPDATE DB */
        if ( $model->db->update('bbn_i18n_exp', ['expression' => $expression, 'actif' => 1 ], [
          'id' => $id,
        ]) ){
          $success = true;
        }
      }

      /** INSERT in DB */
      else {
        $success = $model->db->insert_ignore('bbn_i18n_exp', [
          'expression' => $expression,
          'id_exp' => $row['id_exp'],
          'lang' => $l,
          'actif' => 1
        ]);
      }
    }
    else if ( $row[$l] === '' ){
      if ( $id = $model->db->get_val('bbn_i18n_exp', 'id', [
        'id_exp' => $row['id_exp'],
        'lang' => $l
      ]) ) {
        /** if in a cell of the table the string is deleted it deletes the string from db */
        if ( $model->db->delete('bbn_i18n_exp',[
          'id_exp' => $row['id_exp'],
          'lang' => $l
        ]) ){
          $success = true;
          $deleted = true;
        }
      }
    }
  }
  /** @todo if from I could update the widget after the insert I need to remake the cached model of the widget */
  //$model->get_cached_model(APPUI_I18N_ROOT.'page/data/widgets', ['id_option'=> $model->data['id_option']], true);
  $model->get_cached_model(APPUI_I18N_ROOT.'page/data/strings_table', [
    'id_option' => $model->data['id_option'],
    'routes' => $model->data['routes']
  ], true);
  $model->get_model(APPUI_I18N_ROOT.'actions/generate', ['id_option' => $model->data['id_option']]);
}
return [
  'deleted' => $deleted,
  'row' => $model->data['row'],
  'success' => $success
];