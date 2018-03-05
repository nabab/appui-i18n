<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 15/01/18
 * Time: 16.57
 */


if ( !empty($model->data['limit']) ){
  //I want filter on actif => 1 in bbn-table
  $filters = [[
    'field' => 'bbn_history.usr',
    'operator' => 'eq',
    'value' => $model->inc->user->get_id()
  ], [
    'field' => 'bbn_history2.tst',
    'operator' => 'isnull'
  ]];
  $id_user = $model->inc->user->get_id();
  $id_user = hex2bin($id_user);

  $count = "
    SELECT COUNT(bbn_i18n_exp.id_exp)
            FROM `bbn_i18n_exp`
            JOIN bbn_history
              ON bbn_history.uid = bbn_i18n_exp.id
      LEFT JOIN bbn_history AS bbn_history2
        ON bbn_history2.uid = bbn_history.uid
        AND bbn_history2.tst > bbn_history.tst";
  $query = "
    SELECT 
    bbn_history.usr,
    bbn_i18n.id AS id_exp,
     bbn_i18n_exp.lang AS translation_lang, 
     bbn_i18n_exp.expression AS expression, 
     bbn_i18n.lang AS original_lang, 
     bbn_i18n.exp AS original_exp, 
     bbn_i18n.lang AS original_language, 
     bbn_i18n_exp.lang as translation_lang, 
     bbn_history.dt AS last_modification, 
     bbn_history.opr  AS operation
FROM bbn_history
	JOIN bbn_i18n_exp
		ON bbn_i18n_exp.id = bbn_history.uid
  JOIN bbn_i18n
     ON bbn_i18n.id = bbn_i18n_exp.id_exp
  LEFT JOIN bbn_history AS bbn_history2
  	ON bbn_history2.uid = bbn_history.uid
    AND bbn_history2.tst > bbn_history.tst";

  $grid = new \bbn\appui\grid($model->db, $model->data, [
    'group_by' => 'bbn_i18n_exp.id',
    'extra_fields' => ['bbn_history.usr', 'bbn_history2.tst'],
    'query'=> $query,
    'count' => $count,
    'filters' => $filters
  ]);

  if ( $grid->check() ){
    return $grid->get_datatable();
  }
}