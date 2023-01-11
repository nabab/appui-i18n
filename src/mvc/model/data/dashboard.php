<?php
$res = [
  'success' => false
];
if ($model->hasData('idProject', true)
  && defined('BBN_APP_NAME')
) {
  $idProj = $model->data['idProject'];
  if ($idProj === 'options') {
    $i18nCls = new \bbn\Appui\I18n($model->db, BBN_APP_NAME);
    $langs = $model->inc->options->findI18nLangs();
    $langsIds = [];
    $paths = [];
    foreach ($langs as $l) {
      $langsIds[] = $model->inc->options->fromCode($l, 'languages', 'i18n', 'appui');
      $paths[] = [
        'title' => $model->inc->options->text($l, 'languages', 'i18n', 'appui'),
        'language' => $l,
        'code' => $l,
        'data_widget' => $i18nCls->getOptionsTranslationsWidget($l)
      ];
    }
    return [
      'success' => true,
      'paths' => $paths,
      'langs' => $langsIds
    ];
  }
  else {
    $i18nCls = new \bbn\Appui\I18n($model->db, $idProj);
    $projectCls = new \bbn\Appui\Project($model->db, $idProj);
    $res['langs'] = $projectCls->getLangsIds();
    if ($paths = $projectCls->getPaths()) {
      foreach ($paths as $idx => $path) {
        /** for every project takes the full option of each path */
        if ($opt = $model->inc->options->option($path['id_option'])) {
          $res['paths'][$idx] = $opt;
        }
        /** if language is set takes the cached_model_of the widget */
        if (!empty($opt['language'])) {
          //if the widget has not cache for this method creates the cache
          //IF THE CACHE IS ACTIVE WHEN THE PROJECT IS CHANGED BY THE DROPDOWN IT RETURNS THE WIDGETS OF THE PROJECT APST-APP
          /*if ( empty($i18nCls->cacheHas($opt['id'], 'get_translations_widget')) ){
            //set data in cache $i18nCls->cacheSet($opt['id'], (string)method name, (array)data)
            $i18nCls->cacheSet($opt['id'], 'get_translations_widget',
              $i18nCls->getTranslationsWidget($current_project['id'], $opt['id'])
            );
          }
          $res[$idx]['data_widget'] = $i18nCls->cacheGet($opt['id'], 'get_translations_widget');*/
          $res['paths'][$idx]['data_widget'] = $i18nCls->getTranslationsWidget($idProj, $opt['id']);
        }
        else {
          /** if language is not set returns the array data_widget with locale_dirs and an empty array for result */
          $res['paths'][$idx]['data_widget'] = [];
          $res['paths'][$idx]['data_widget']['locale_dirs'] = [];
          $res['paths'][$idx]['data_widget']['result'] = [];
        }
        $res['paths'][$idx]['title'] = $model->inc->options->text($opt['id_parent']).'/'.$opt['text'];
      }
      $res['success'] = true;
    }
  }
}
return $res;
