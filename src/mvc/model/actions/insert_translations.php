<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 08/01/18
 * Time: 13.25
 */


// row sent by post
if ( !empty($model->data['row']['id_exp'])){
  $success = false;
  $row = $model->data['row'];
  $langs = $model->data['langs'];
//receives configured langs from the post

  foreach( $langs as $l => $val ){
    if ( !empty($row[$l]) ){
      $expression = $row[$l];

      //case update the expression exists in this lang ($l)
      if ( $model->db->get_val('bbn_i18n_exp', 'expression', [
        'id_exp' => $row['id_exp'],
        'lang' => $l
      ]) ){
        /*die(var_dump($row['id_exp'], $l,$model->db->update('bbn_i18n_exp', ['expression' => $expression ], [
          'id_exp' => $row['id_exp'],
          'lang' => $l,

        ]) ));*/
        if ( $model->db->update('bbn_i18n_exp', ['expression' => $expression ], [
          'id_exp' => $row['id_exp'],
          'lang' => $l,
        ]) ){
          $success = true;
        };
      }
      //case insert
      else {
        if ( $model->db->insert('bbn_i18n_exp', [
          'expression' => $expression,
          'id_exp' => $row['id_exp'],
          'lang' => $l,
          'actif' => 1
        ]) ){
          $success = true;
        };
      }
    }
  }

  return [ 'success' => $success ];
}