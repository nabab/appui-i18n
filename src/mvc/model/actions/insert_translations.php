<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 08/01/18
 * Time: 13.25
 */

if ($model->hasData(['id_project', 'id_option', 'row'])
  && !empty($model->data['row']['id_exp'])
  && defined('BBN_APP_NAME')
) {
  $isOptions = ($model->data['id_project'] === 'options') && !\bbn\Str::isUid($model->data['id_option']);
  $i18nCls = new \bbn\Appui\I18n($model->db, $isOptions ? BBN_APP_NAME : $model->data['id_project']);
  /** @var array $row the row sent by strings table */
  $row = $model->data['row'];
  /** @var array $langs sent by strings table*/
  $langs = $model->data['langs'];
  $deleted = [];
  $modified = [];
  $widget;

  foreach ($langs as $l) {
    if (!empty($row[$l.'_db'])) {
      if ($i18nCls->insertOrUpdateTranslation($row['id_exp'], $row[$l . '_db'], $l)) {
        $modified[] = $l;
      }
      else {
        $success = false;
      }
    }
    else {
      if ($i18nCls->deleteTranslation($row['id_exp'], $l)) {
        $deleted[] = $l;
      }
    }
  }

  if (!empty($modified) || !empty($deleted)) {
    //replace the row in the cache of the table
    $tmp = $i18nCls->cacheGet($model->data['id_option'], $isOptions ? 'get_options_translations_table' : 'get_translations_table');
    if (!empty($tmp) && !empty($tmp['strings'])){
      $idx = \bbn\X::find($tmp['strings'], ['id_exp' => $row['id_exp']]);
      if (!is_null($idx)) {
        foreach ($modified as $mod) {
          //change the updated string in the row of the cache
          $tmp['strings'][$idx][$mod . '_db'] = $row[$mod . '_db'];
        }
        foreach ($deleted as $del) {
          //change the removed string in the row of the cache
          $exp_changed = $model->data['row'][$del .'_db'];
          $tmp['strings'][$idx][$del . '_db'] = $row[$del . '_db'];
        }
        $i18nCls->cacheSet(
          $model->data['id_option'],
          $isOptions ? 'get_options_translations_table' : 'get_translations_table',
          $tmp
        );
        //remake the cache of the widget basing on new data
        if ($isOptions) {
          $i18nCls->cacheSet(
            $model->data['id_option'],
            'get_options_translations_widget',
            $i18nCls->getOptionsTranslationsWidget($model->data['id_option'])
          );
          $widget = $i18nCls->cacheGet($model->data['id_option'], 'get_options_translations_widget');
        }
        else {
          $i18nCls->cacheSet(
            $model->data['id_option'],
            'get_translations_widget',
            $i18nCls->getTranslationsWidget($model->data['id_project'], $model->data['id_option'])
          );
          $widget = $i18nCls->cacheGet($model->data['id_option'], 'get_translations_widget');
        }
      }
    }
  }

  return [
    'widget' => $widget,
    'modified_langs' => $modified,
    'deleted' => $deleted,
    'row' => $row,
    'success' => !empty($modified) || !empty($deleted)
  ];
}
return ['success' => false];
