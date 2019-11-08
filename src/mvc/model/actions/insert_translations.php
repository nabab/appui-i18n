<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 08/01/18
 * Time: 13.25
 */


$success = false;
/** at @change the table send row */
if ( !empty($model->data['row']['id_exp'])){
  
  /** @var  $translation instantiate the class appui\i18n*/
  $translation = new \bbn\appui\i18n($model->db);
  /** @var $row the row sent by strings table */
  $row = $model->data['row'];
  /** @var (array) $langs sent by strings table*/
  $langs = $model->data['langs'];
  $deleted = [];
  $modified_langs = [];
  $widget;


  foreach( $langs as $l ){
    if ( !empty($row[$l.'_db']) ){

      /** @var $expression the string */
      $expression = $row[$l.'_db'];
      /** @var $id if the $id of the string exists */

      if ( $id = $model->db->select_one('bbn_i18n_exp', 'id', [
        'id_exp' => $row['id_exp'],
        'lang' => $l
      ]) ){

        /** UPDATE DB */
        if ( $model->db->update('bbn_i18n_exp', ['expression' => $expression], [
          'id' => $id,
          'lang' => $l
        ]) ){
          $modified_langs[] = $l;
          $success = true;
        }
      }

      /** INSERT in DB */
      else {
        $modified_langs[] = $l;
        $success = $model->db->insert_ignore('bbn_i18n_exp', [
          'expression' => stripslashes($expression),
          'id_exp' => $row['id_exp'],
          'lang' => $l
        ]);
      }
    }

  }

  if( !empty( $model->data['to_delete'] ) ){
    $to_delete = $model->data['to_delete'];
    foreach ( $to_delete as $del ){
      if ( $id = $model->db->select_one('bbn_i18n_exp', 'id', [
        'id_exp' => $row['id_exp'],
        'lang' => $del
      ]) ) {
        /** if in a cell of the table the string is deleted it deletes the string from db */
        if ( $model->db->delete('bbn_i18n_exp',[
          'id_exp' => $row['id_exp'],
          'lang' => $del,
          'id' => $id
        ]) ){

          $success = true;
          $deleted[] = $del;
        }
      }
    }
  }

  if ( !empty($modified_langs) && !empty($success) ){
    //replace the row in the cache of the table

    // $tmp the cache of the table
    $tmp = $translation->cache_get($model->data['id_option'], 'get_translations_table');

    if ( !empty($tmp) && !empty($tmp['strings'][$model->data['row_idx']]) && !empty($modified_langs)){
      $widget;  
      //$tmp = $translation->cache_get($model->data['id_option'], 'get_translations_table');
      foreach ( $modified_langs as $mod ){

        //change the updated string in the row of the cache
        $exp_changed = $model->data['row'][$mod.'_db'];
        $tmp['strings'][$model->data['row_idx']][$mod.'_db'] = $exp_changed;

      }
      if ( !empty($to_delete) ){
        foreach( $to_delete as $del ){
          //change the updated string in the row of the cache
          $exp_changed = $model->data['row'][$del .'_db'];
          $tmp['strings'][$model->data['row_idx']][$del .'_db'] = $exp_changed;
        }
      }

      $translation->cache_set($model->data['id_option'], 'get_translations_table',
        $tmp
      );
      //remake the cache of the widget basing on new data
      $translation->cache_set($model->data['id_option'], 'get_translations_widget',
        $translation->get_translations_widget($model->data['id_project'],$model->data['id_option'])
      );
      $widget = $translation->cache_get($model->data['id_option'], 'get_translations_widget');

    }
  }

  /*if ( !empty($modified_langs) ){
    //replace the row in the cache of the table

    // $tmp the cache of the table
    $tmp = $translation->cache_get($model->data['id_option'], 'get_translations_table');

    if ( !empty($tmp) && !empty($tmp['strings'][$model->data['row_idx']]) && !empty($modified_langs)){

      $tmp = $translation->cache_get($model->data['id_option'], 'get_translations_table');
      foreach($modified_langs as $mod){
        if( !empty($mod) ){
          //change the updated string in the row of the cache
          $exp_changed = $model->data['row'][$mod];
          $tmp['strings'][$model->data['row_idx']][$mod]['translations_db'] = $exp_changed;
        }
      }
      $translation->cache_set($model->data['id_option'], 'get_translations_table',
        $tmp
      );
      //remake the cache of the widget basing on new data
      $translation->cache_set($model->data['id_option'], 'get_translations_widget',
        $translation->get_translations_widget($model->data['id_project'],$model->data['id_option'])
      );
      $widget = $translation->cache_get($model->data['id_option'], 'get_translations_widget');

    }
  }*/

  return [
    'widget' => $widget,
    'modified_langs' => $modified_langs,
    'deleted' => $deleted,
    'row' => $model->data['row'],
    'success' => $success
  ];
}
