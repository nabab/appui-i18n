<?php
/*
 * Describe what it does!
 *
 **/



/** @var $this \bbn\mvc\model*/



if ( $model->data['id_option'] ) {
  $success = false;
  /** @var  $translation instantiate the class i18n */
  $translation = new \bbn\appui\i18n($model->db);
  $locale = $translation->get_locale_dir_path($model->data['id_option']);
  if ( is_dir($locale) ){
    $success =  \bbn\file\dir::delete($locale, true);
  }
	return [
    'success' => $success,
  ];
}
