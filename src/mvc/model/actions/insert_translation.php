<?php
use bbn\Appui\I18n;

if ($model->hasData(['id_exp', 'expression', 'lang'], true)) {
  $i18nCls = new I18n($model->db);
  return [ 'success' => $i18nCls->insertOrUpdateTranslation(
    $model->data['id_exp'],
    $model->data['expression'],
    $model->data['lang']
  )];
}
