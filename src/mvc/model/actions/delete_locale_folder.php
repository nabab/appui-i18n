<?php



/** @var $this \bbn\Mvc\Model*/
if ( !empty($model->data['id_option']) && !empty($model->data['id_project'] )) {
  $success = false;
  /** @var  $translation instantiate the class i18n */
  $translation = new \bbn\Appui\I18n($model->db);
  $locale = $translation->getLocaleDirPath($model->data['id_option']);
  if ( is_dir($locale) ){
    $success =  \bbn\File\Dir::delete($locale, true);
  }
	return [
    'success' => $success,
  ];
}
