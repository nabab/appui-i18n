<?php
use bbn\Appui\I18n;
if ($model->hasData(['id_project', 'id_option'], true)) {
  $isOptions = $model->data['id_project'] === 'options';
  $translation = new I18n($model->db, $isOptions ? null : $model->data['id_project']);
  if ($generated = $translation->generateFiles($model->data['id_option'], $model->data['languages'] ?? [], $isOptions ? 'options' : 'files')) {
    /** @var array The data of the table in cache */
    $strings = $translation->cacheGet($model->data['id_option'], $isOptions ? 'get_options_translations_table' : 'get_translations_table');
    /** @var array The data of the widget in the cache*/
    $widget  = $translation->cacheGet($model->data['id_option'], $isOptions ? 'get_options_translations_widget' : 'get_translations_widget');
    return array_merge([
      'success' => true,
      'widget' => $widget ?: null,
      'strings' => empty($strings) ? [] : $strings['strings']
    ], $generated);
  }
}
return ['success' => false];
