<?php
/*
 * Describe what it does!
 *
 **/

/** @var $this \bbn\mvc\model*/

$model->data['success'] = false;

if ( !empty($id_user = $model->inc->user->get_id()) ){

  $is_admin =  $model->db->val_by_id("bbn_users", "admin", $id_user);


  /*IMPORTANT when I modify the table bbn_i18n_exp bbn_i18n.last_modified has to be modified at the same id of bbn_i18n_exp.id_exp*/

  $user_history = $model->db->get_rows("
    SELECT bbn_i18n_exp.expression, bbn_i18n_exp.lang, bbn_i18n_exp.id_exp,
    bbn_i18n.last_modified, bbn_users.nom AS 'name'
      FROM bbn_i18n
      JOIN bbn_users 
        ON bbn_i18n.id_user = bbn_users.id 
      JOIN bbn_i18n_exp
        ON bbn_i18n.id = bbn_i18n_exp.id_exp
     WHERE bbn_i18n.id_user = ?
     ORDER BY  bbn_i18n.last_modified DESC
     LIMIT 25  
    ", hex2bin($id_user)
  );


  //the complete history for all users will be load just if user is admin
  /*Send is_admin is sent by the post when click on the homepage button
  this data will be created just if the user is admin and just at the moment of the click*/
  if ( !empty($model->data['is_admin'])  ){

    $complete_history = $model->db->get_rows("
      SELECT bbn_i18n_exp.expression, bbn_i18n_exp.lang, bbn_i18n_exp.id_exp, 
      bbn_i18n.last_modified, bbn_users.nom AS name
        FROM bbn_i18n
        JOIN bbn_users
          ON bbn_i18n.id_user = bbn_users.id
        JOIN bbn_i18n_exp
          ON bbn_i18n.id = bbn_i18n_exp.id_exp   
        LIMIT 25");
    if ( !empty($model->data['id_exp']) && !empty($model->data['lang']) && !empty($model->data['search']) ){

    }
  }
  $model->data['success'] = true;

  return[
    'is_admin' => $is_admin,
    'user_history' => $user_history,
    'complete_history' => $complete_history,
    'success' => $model->data['success']
  ];
}





