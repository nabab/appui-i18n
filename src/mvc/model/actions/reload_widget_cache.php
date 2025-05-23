<?php
use bbn\Appui\I18n;

if ($model->hasData(['id_project', 'id_option'], true)) {
  $idProject = $model->data['id_project'];
  $idOption = $model->data['id_option'];
  $isOptions = $idProject === 'options';
  $i18nCls = new I18n($model->db, $isOptions ? null : $idProject);
  return [
    'success' => true,
    'data' => $isOptions ?
      $i18nCls->getOptionsTranslationsWidget($idOption) :
      $i18nCls->getTranslationsWidget($idOption)
  ];
}

return ['success' => false];
