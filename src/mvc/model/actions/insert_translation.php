<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 08/01/18
 * Time: 13.25
 */


// row sent by post
if ( !empty($model->data['id_exp']) && !empty($model->data['expression'])){
  $success = false;
  //case update the expression exists in this lang ($model->data['translation_lang'])
  if ( $expression = $model->db->get_val('bbn_i18n_exp', 'expression', [
    'id_exp' => $model->data['id_exp'],
    'lang' => $model->data['translation_lang']
  ]) ){
    $success = $model->db->update('bbn_i18n_exp', ['expression' => $model->data['expression'] ], [
      'id_exp' => $model->data['id_exp'],
      'lang' => $model->data['translation_lang']
    ]);
  }
  //case insert
  else {
    $success = $model->db->insert('bbn_i18n_exp', [
    'expression' => $model->data['expression'],
    'id_exp' => $model->data['id_exp'],
    'lang' => $model->data['translation_lang']
    ]);
  }
  return [ 'success' => $success ];
}