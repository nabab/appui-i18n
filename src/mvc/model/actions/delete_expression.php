<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 27/02/18
 * Time: 12.08
 */

//Deletes the expression from db 
$success = false;
if ( $model->data['id_exp'] ){
  if ( !empty($model->db->rselectAll('bbn_i18n_exp', [], ['id_exp' => $model->data['id_exp'] ]) ) ){
    if ( $model->db->delete('bbn_i18n_exp', ['id_exp' => $model->data['id_exp'] ]) ) {
      $success = $model->db->delete('bbn_i18n', ['id' => $model->data['id_exp'] ]);
    }
  }
  return [ 'success' => $success ];
}