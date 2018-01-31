<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 15/01/18
 * Time: 16.57
 */


if ( !empty($model->data['limit']) ){
  //I want filter on actif => 1 in bbn-table
  $filters = [
    'bbn_history.usr' => $model->inc->user->get_id()
  ];

  $id_user = $model->inc->user->get_id();

  $count = "
    SELECT COUNT(bbn_i18n_exp.id)
            FROM `bbn_i18n_exp` 
            JOIN bbn_history
              ON bbn_history.uid = bbn_i18n_exp.id
           WHERE bbn_i18n_exp.id = bbn_history.uid
    ";
  $query = "
    SELECT bbn_i18n_exp.id, expression, bbn_i18n_exp.lang, bbn_i18n_exp.actif AS 'translation_lang', bbn_users.nom AS 
    'user', bbn_history.dt as 'last_modification', bbn_history.opr as 'operation', bbn_i18n.exp AS 'original_expression', bbn_i18n.lang AS 'original_lang'
      FROM `bbn_i18n_exp` 
      JOIN bbn_history
        ON bbn_history.uid = bbn_i18n_exp.id
      JOIN bbn_users
        ON bbn_history.usr = bbn_users.id
      JOIN bbn_i18n
        ON bbn_i18n.id = bbn_i18n_exp.id_exp
     ";





  $grid = new \bbn\appui\grid($model->db, $model->data, [

    'query'=> $query,
    'count' => $count,
    //momentaneamente ho tolto filters, vorrei usare quelli della tabella opzionalmente...

    'filters' => $filters
  ]);

  if ( $grid->check() ){
    return $grid->get_datatable();
  }
}


