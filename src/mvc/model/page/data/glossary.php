<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 17/01/18
 * Time: 12.35
 */
if ( !empty($model->data['limit']) &&  !empty($model->data['data']['translation_lang']) && !empty($model->data['data']['source_lang']) ){

  $trans_lang_code = $model->data['data']['translation_lang'];
  $source_code = $model->data['data']['source_lang'];
  $filters = [
    'bbn_i18n.actif' => 1,
    'bbn_i18n.lang' => $source_code
  ];

  $query = "
    SELECT bbn_i18n.exp AS original_exp, bbn_i18n.id AS idExp, bbn_history.usr AS id_user,
      (SELECT bbn_i18n_exp.expression 
       FROM bbn_i18n_exp 
       WHERE bbn_i18n_exp.id_exp = idExp
        AND bbn_i18n_exp.lang = '$trans_lang_code'
        AND bbn_i18n_exp.actif = 1
      ) AS translation
    FROM bbn_i18n
    JOIN bbn_history
      ON bbn_history.uid = bbn_i18n.id";

  $count = "
    SELECT COUNT(bbn_i18n.id) 
    FROM bbn_i18n";

  //if I give to $grid 'table' => 'bbn_i18n' I don't have data
  $grid = new \bbn\appui\grid($model->db, $model->data, [

    'query'=> $query,
    'count' => $count,
    'group_by' => 'bbn_i18n.id',
    'filters'=> $filters


  ]);

  if ( $grid->check() ){
    return $grid->get_datatable();
  }
}

/* QUERY FUNZIONANTE DI MIRKO
 * $query = "
    SELECT bbn_i18n.exp AS original_expression, bbn_i18n.id AS idExp, bbn_history.usr AS id_user,
      (SELECT bbn_i18n_exp.expression
       FROM bbn_i18n_exp
       WHERE bbn_i18n_exp.id_exp = idExp
        AND bbn_i18n_exp.lang = '$trans_lang_code'
      ) AS translation
    FROM bbn_i18n
    JOIN bbn_history
      ON bbn_history.uid = bbn_i18n.id";
 */