<?php
/**
 * Created by PhpStorm.
 * User: bbn
 * Date: 22/03/18
 * Time: 11.54
 */

//Deletes the property language for the given option
if ( $model->data['id_option'] && $model->data['language'] && $model->data['id_project']){
  $success = false;
  $translation = new \bbn\appui\i18n($model->db, $model->data['id_project']);

  //if the property 'language' exists for this option
  if ( !empty($model->inc->options->get_prop($model->data['id_option'], 'language')) ){
    //unset the property
    $model->inc->options->unset_prop($model->data['id_option'], 'language');
    //remake the cached of the widget
    $success = true;
    $model->inc->options->delete_cache($model->data['id_project'], true);
  }
  return [
    'success' => $success
  ];
}
