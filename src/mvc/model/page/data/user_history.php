<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 15/01/18
 * Time: 16.57
 */

//data for user history table
if ( !empty($model->data['limit']) ){

  $id_user = $model->inc->user->get_id();

  $id_user = $id_user;

  $grid = new \bbn\appui\grid($model->db, $model->data, [
    'tables' => ['bbn_i18n_exp'],
    'fields' => [
      'bbn_i18n_exp.id_exp', 'expression',
      'translation_lang' => 'bbn_i18n_exp.lang',
      'last_modification' => 'MAX(bbn_history.dt)',
      'operation' => 'bbn_history.opr',
      'original_exp' => 'bbn_i18n.exp',
      'original_lang'=> 'bbn_i18n.lang',
      'user' =>  'bbn_history.usr'

    ],
    'join' => [[
      'table' => 'bbn_history',
      'on' => [
        'logic' => 'AND',
        'conditions' => [[
          'field' => 'bbn_history.uid',
          'operator' => 'eq',
          'exp' => 'bbn_i18n_exp.id'
        ]]
      ],
    ],[
      'table' => 'bbn_i18n',
      'on' => [
        'logic' => 'AND',
        'conditions' => [[
          'field' => 'bbn_i18n.id',
          'operator' => 'eq',
          'exp' => 'bbn_i18n_exp.id_exp'
        ]]
      ],
    ]],
    'filters' => [[
      'field' => 'bbn_history.usr',
      'operator' => '=',
      'value' => $id_user
    ]],
    'group_by' => 'bbn_i18n_exp.id',

  ]);


  if ( $grid->check() ){
    return $grid->get_datatable();
  }
}
