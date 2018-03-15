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
  $langs = $model->data['langs'];
//receives configured langs from the post

  foreach( $langs as $l => $val ){
    if ( !empty($row[$l]) ){

      $expression = $row[$l];

      //case update the expression exists in this lang ($l)
      if ( $id = $model->db->get_val('bbn_i18n_exp', 'id', [
        'id_exp' => $row['id_exp'],
        'lang' => $l
      ]) ){
        if ( $model->db->update('bbn_i18n_exp', ['expression' => $expression ], [
          'id' => $id,

        ]) ){
          $success = true;
        }
      }
      //case insert
      else {
        $success = $model->db->insert('bbn_i18n_exp', [
          'expression' => $expression,
          'id_exp' => $row['id_exp'],
          'lang' => $l,
          'actif' => 1
        ]);
      }
    }
  }
}
return [
  'row' => $model->data['row'],
  'success' => $success
];