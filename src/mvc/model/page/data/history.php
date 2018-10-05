<?php
/**
* Created by BBN Solutions.
* User: Loredana Bruno
* Date: 15/01/18
* Time: 16.57
*/


if ( !empty($model->data['limit']) ){

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

      'group_by' => 'bbn_i18n_exp.id',

    ]);


  /*$query = "
    SELECT bbn_i18n_exp.id_exp, expression, bbn_i18n_exp.lang AS translation_lang, MAX(bbn_history
    .dt) as last_modification, bbn_history.opr as operation, bbn_i18n.exp AS original_exp, bbn_i18n.lang AS original_lang,
(SELECT bbn_users.nom FROM bbn_history JOIN bbn_users ON bbn_users.id = bbn_history.usr WHERE bbn_history.uid = bbn_i18n_exp.id ORDER BY bbn_history.dt DESC LIMIT 1) as user
      FROM `bbn_i18n_exp`
      JOIN bbn_history
        ON bbn_history.uid = bbn_i18n_exp.id
      JOIN bbn_users
        ON bbn_history.usr = bbn_users.id
      JOIN bbn_i18n
        ON bbn_i18n.id = bbn_i18n_exp.id_exp
        ";*/

  /*$grid = new \bbn\appui\grid($model->db, $model->data, [
    'extra_fields' => ['bbn_i18n.exp'],
    'group_by' => 'bbn_i18n_exp.id',
    'query'=> $query,
    'count' => $count,
    'filters' => $filters
  ]);*/

  if ( $grid->check() ){
    return $grid->get_datatable();
  }

}
