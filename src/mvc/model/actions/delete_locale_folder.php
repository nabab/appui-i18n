<?php
use bbn\Appui\I18n;
use bbn\File\Dir;

if (!empty($model->data['id_option'])
  && !empty($model->data['id_project'])
) {
  $success = false;
  $translation = new I18n($model->db);
  $locale = $translation->getLocaleDirPath($model->data['id_option']);
  if (is_dir($locale)) {
    $success =  Dir::delete($locale, true);
  }

	return [
    'success' => $success,
  ];
}
