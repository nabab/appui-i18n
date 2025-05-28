<?php

use bbn\Appui\I18n;

$success = false;
if ($model->hasData(['project', 'path', 'expression', 'translations'], true)) {
  $i18nCls = new I18n($model->db, $model->data['project']);
  if (($projectLang = $i18nCls->getLanguage($model->data['path']))
    && ($idExp = $i18nCls->getId($model->data['expression'], $projectLang))
  ) {
    $success = true;
    $isOptions = $model->data['id_project'] === 'options';
    foreach ($model->data['translations'] as $lang => $translation) {
      if (!$i18nCls->insertOrUpdateTranslation($idExp, $translation, $lang)) {
        $success = false;
      }
    }
  }
}

return [
  'success' => $success
];
