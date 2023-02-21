<?php
  if (($idOption = $model->data['id_option'])
    && ($idProject = $model->data['id_project'])
  ) {
  $isOptions = $idProject === 'options';
  $i18nCls = new \bbn\Appui\I18n($model->db, $isOptions ? null : $idProject);
  return [
    'data' => $isOptions ?
      $i18nCls->getOptionsTranslationsWidget($idOption) :
      $i18nCls->getTranslationsWidget($idProject, $idOption),
    'success' => true
  ];
}
return ['success' => false];
