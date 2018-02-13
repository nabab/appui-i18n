<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 08/01/18
 * Time: 13.25
 */


// by pressing enter key I post the editedRow
if ( !empty($model->data['row']) ){
  $row = $model->data['row'];
  //if the row's id (the id of expression) is !empty
  if ( !empty($row['id']) ){
    $succ = false;
    //Unset property I don't need from the row, in $row will remain just the langs and the corresponding expression
    if( isset($row['id_user'], $row['last_modified'], $row['exp'], $row['lang']) ){
      $id_exp = $row['id'];
      unset($row['id_user'], $row['last_modified'], $row['exp'], $row['lang'], $row['id'], $row['actif']);
    }
    //loop on all langs and expression arriving from js
    foreach ( $row as $r => $val ){

      //$old_exp is the expression present in db for the lang $r
      if ( ( ($old_exp = $model->db->select_one('bbn_i18n_exp',
            'expression', [
              'id_exp' => $id_exp,
              'lang' => $r
            ])) !== false ) &&
        //case in which the expression has been modified
        ($old_exp !== $val)
      ){
        if ( $model->db->update('bbn_i18n_exp',
          ['expression' => $val], [
            'id_exp' => $id_exp,
            'lang' => $r
          ])
        ){
          $succ = true;
        }
      }

      //case in which $old_exp doesn't exist for the language §$r
      else if ( !empty($val) && ( $old_exp === false ) && $model->db->insert('bbn_i18n_exp',
          [ 'expression' => $val,
            'id_exp' => $id_exp,
            'lang' => $r
          ])
      ){
        $succ = true;
      }
    }
    return ['success' => $succ];
  }
}