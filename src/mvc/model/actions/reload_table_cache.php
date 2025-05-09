<?php
if (($idOption = $model->data['id_option'])
  && ($idProject = $model->data['id_project'])
) {
  $isOptions = $idProject === 'options';
  $i18nCls = new \bbn\Appui\I18n($model->db, $isOptions ? null : $idProject);
  if ($isOptions) {
    $data = $i18nCls->getOptionsTranslationsTable($idOption);
    $widget = $i18nCls->getOptionsTranslationsWidget($idOption);
  }
  else {
    $data = $i18nCls->getTranslationsTable($idProject, $idOption);
    $widget = $i18nCls->getTranslationsWidget($idOption);
  }
  return [
    'data' => $data,
    'widget' => $widget,
    'success' => true
  ];
}
return ['success' => false];
