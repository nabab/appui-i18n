<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 17/01/18
 * Time: 12.35
 */
/*$count è sbagliato, se metto il where bbn_i18n_exp.lang like $lang_code ho un errore di db perchè è come se mettesse nella richiesta 2 volte il where.

SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'WHERE  ( `bbn_i18n_exp`.`lang` LIKE 'fr' )' at line 4
SELECT COUNT(bbn_i18n_exp.id)
            FROM `bbn_i18n_exp`
       WHERE bbn_i18n_exp.lang  LIKE 'fr'
     WHERE  ( `bbn_i18n_exp`.`lang` LIKE 'fr' )
*/

if ( !empty($model->data['limit']) &&  !empty($lang_code = $model->data['data']['lang']) ){
  $count = "
    SELECT COUNT(bbn_i18n_exp.id)
            FROM `bbn_i18n_exp` 
         
    ";

  $query = "
    SELECT expression,bbn_i18n_exp.lang, bbn_i18n.exp AS 'original_exp', bbn_i18n.lang AS 'original_lang', bbn_users.nom AS 'user'
    FROM bbn_i18n_exp
    JOIN bbn_i18n
      ON bbn_i18n.id = bbn_i18n_exp.id_exp
      LEFT JOIN bbn_history
      ON bbn_history.uid = bbn_i18n_exp.id
      LEFT JOIN bbn_users 
      ON bbn_users.id = bbn_history.usr
     ";

  $grid = new \bbn\appui\grid($model->db, $model->data, [
    'query'=> $query,
    'count' => $count,
    //momentaneamente ho tolto filters, vorrei usare quelli della tabella opzionalmente...
    'filters'=> ['bbn_i18n_exp.lang' => $lang_code, 'bbn_i18n_exp.actif' => 1]

  ]);

  if ( $grid->check() ){
    return $grid->get_datatable();
  }
}


