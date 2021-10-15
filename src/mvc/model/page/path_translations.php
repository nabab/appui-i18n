<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 14/12/17
 * Time: 13.05
 */

/* @var string ID of the path to analyze is expected */

if (!empty($model->data['id_option'])) {
  $id_project = $model->data['id_project'] ?? false;
  //REMAKE THE CACHE OF THE WIDGETS
  /** @var  $translation instantiate the class Appui\I18n*/
  $translation = new \bbn\Appui\I18n($model->db, $id_project);

  //if the table has no cache it creates cache
  if (!empty($id_project)
      && (!empty($model->data['force'])
      ||!$translation->cacheHas($model->data['id_option'], 'get_translations_table'))
  ) {
    //set data in cache $translation->cacheSet($id_option, (string)method name, (array)data)
    $translation->cacheSet(
      $model->data['id_option'], 'get_translations_table',
      $translation->getTranslationsTable($id_project, $model->data['id_option'])
    );
  }

  $res = $translation->cacheGet($model->data['id_option'], 'get_translations_table');






  //case of project 'options'
  if (!empty($id_project) && ($id_project === 'options')) {
    /**case project options*/
    /** @var  $cfg get the property i18n from option cfg to send it to the find_options*/
    if ($cfg = $model->inc->options->getCfg($model->data['id_option'])) {
      if (!empty($cfg['i18n'])) {
        $model->data['language'] = $cfg['i18n'];
        $res = $model->getModel(
          APPUI_I18N_ROOT.'options/find_options',
          true,
          true
        );
      }
    }
  }
  $uid_languages = $model->inc->options->fromCode('languages', 'i18n', 'appui');
  if ( $languages = $model->inc->options->fullOptions($uid_languages) ){
    $filter = array_filter($languages, function($v) {
      return !empty($v['primary']);
    });
    $primaries = array_values($filter);
  }
  return $model->addData([
    'primary' => $primaries,
    'id_project' => $id_project ?: $id_project,
    'res' => $res,
    'pageTitle' => $model->inc->options->text($model->data['id_option'])
  ])->data;
}
