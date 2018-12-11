<?php
/**
 * Created by PhpStorm.
 * User: bbn
 * Date: 22/03/18
 * Time: 11.54
 */

//Deletes the property language for the given option
if ( $model->data['id_option'] && $model->data['language']){
  $success = false;
  $translation = new \bbn\appui\i18n($model->db);

  //if the property 'language' exists for this option
  if ( !empty($model->inc->options->get_prop($model->data['id_option'], 'language')) ){
    //unset the property
    $model->inc->options->unset_prop($model->data['id_option'], 'language');
    //remake the cached of the widget
    //$data_widget = $translation->get_translations_widget($projects[$i]['id'],$res[$idx]['id']);
    $success = true;
  }
  return [
    'success' => $success
  ];
}
