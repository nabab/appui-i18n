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
  $data = $model->db->get_rows('
  SELECT bbn_i18n.id, bbn_i18n.exp AS original_expression, bbn_i18n_exp.expression AS translation, IFNULL(hu.usr, hi
  .usr)    AS 
  id_user
    FROM `bbn_i18n`
    LEFT JOIN bbn_i18n_exp
      ON bbn_i18n_exp.id_exp = bbn_i18n.id
     AND bbn_i18n_exp.lang = ?
    LEFT JOIN bbn_history AS hi
      ON hi.uid = bbn_i18n_exp.id
     AND hi.opr LIKE \'INSERT\'
    LEFT JOIN bbn_history AS hu
      ON hu.uid = bbn_i18n_exp.id
     AND hu.opr LIKE \'UPDATE\'
   WHERE bbn_i18n.lang = ?
   GROUP BY bbn_i18n.id',
    $trans_lang_code, $source_code );
  return [
    'data' => $data
  ];
}
/*I wasn't able to adapt this query
SELECT bbn_i18n.exp, bbn_i18n_exp.expression, IFNULL(hu.usr, hi.usr) AS id_user
FROM `bbn_i18n`
    LEFT JOIN bbn_i18n_exp
        ON bbn_i18n_exp.id_exp = bbn_i18n.id
AND bbn_i18n_exp.lang = 'fr'
    LEFT JOIN bbn_history AS hi
        ON hi.uid = bbn_i18n_exp.id
AND hi.opr LIKE 'INSERT'
    LEFT JOIN bbn_history AS hu
        ON hu.uid = bbn_i18n_exp.id
AND hu.opr LIKE 'UPDATE'
WHERE bbn_i18n.lang = 'en'
GROUP BY bbn_i18n.id
to appui\grid
*/
/*

if ( !empty($model->data['limit']) &&  !empty($model->data['data']['translation_lang']) && !empty($model->data['data']['source_lang']) ){

  $trans_lang_code = $model->data['data']['translation_lang'];
  $source_code = $model->data['data']['source_lang'];
  $count = "
    SELECT COUNT(bbn_i18n_exp.id_exp)
      FROM `bbn_i18n_exp`
      JOIN bbn_i18n
        ON  bbn_i18n.id = bbn_i18n_exp.id_exp
    ";
	
  $query = "
  	SELECT bbn_i18n.exp, bbn_i18n_exp.expression, IFNULL(hu.usr, hi.usr) AS id_user
    
      LEFT JOIN bbn_i18n_exp
        ON bbn_i18n_exp.id_exp = bbn_i18n.id
    LEFT JOIN bbn_history AS hi
        ON hi.uid = bbn_i18n_exp.id
        AND hi.opr LIKE 'INSERT'
    LEFT JOIN bbn_history AS hu
        ON hu.uid = bbn_i18n_exp.id
        AND hu.opr LIKE 'UPDATE'
";



  $grid = new \bbn\appui\grid($model->db, $model->data, [
    'table' => 'bbn_i18n',
    'query'=> $query,
    'count' => $count,
    'extra_fields' => ['hu.usr', 'bbn_i18n.lang', 'bbn_i18n_exp.lang', 'hi.usr'],
    'group_by' => 'bbn_i18n.id',
    //momentaneamente ho tolto filters, vorrei usare quelli della tabella opzionalmente...
    'filters'=> [
      'bbn_i18n.lang' => $source_code,
      'bbn_i18n_exp.actif' => 1,
      'bbn_i18n_exp.lang' => $trans_lang_code
    ]
    

  ]);

  if ( $grid->check() ){
    return $grid->get_datatable();
  }
}*/