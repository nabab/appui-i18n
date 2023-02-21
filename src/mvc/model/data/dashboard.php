<?php
$res = [
  'success' => false,
  'paths' => []
];
if ($model->hasData('idProject', true)
  && defined('BBN_APP_NAME')
) {
  $isOptions = $model->data['idProject'] === 'options';
  $idProj = $isOptions ? BBN_APP_NAME : $model->data['idProject'];
  $i18nCls = new \bbn\Appui\I18n($model->db, $idProj);
  $projectCls = new \bbn\Appui\Project($model->db, $idProj);
  $primaries = $projectCls->getPrimariesLangs();
  if ($isOptions) {
    $res['langs'] = array_map(function($l) use($primaries) {
      return \bbn\X::getField($primaries, ['code' => $l], 'id');
    }, $model->inc->options->findI18nLocales());
  }
  else {
    $res['langs'] = $projectCls->getLangsIds();
  }
  if ($paths = $projectCls->getPaths()) {
    foreach ($paths as $path) {
      if ($opt = $model->inc->options->option($path['id_option'])) {
        $parent = $model->inc->options->option($opt['id_parent']);
        $opt['title'] = $parent['text'].'/'.$opt['text'];
        if ($isOptions) {
          if ((($parent['code'] === 'app')
              && ($opt['code'] === 'main'))
            || (($parent['code'] === 'lib')
              && (str_starts_with($opt['code'], 'appui-')))
          ) {
            if ($i18nCls->cacheHas($opt['id'], 'get_options_translations_widget')) {
              $opt['data_widget'] = $i18nCls->cacheGet($opt['id'], 'get_options_translations_widget');
            }
            else {
              $opt['data_widget'] = $i18nCls->getOptionsTranslationsWidget($opt['id']);
            }
            $res['paths'][] = $opt;
          }
        }
        else {
          if (!empty($opt['language'])) {
            if ($i18nCls->cacheHas($opt['id'], 'get_translations_widget')) {
              $opt['data_widget'] = $i18nCls->cacheGet($opt['id'], 'get_translations_widget');
            }
            else {
              $opt['data_widget'] = $i18nCls->getTranslationsWidget($idProj, $opt['id']);
            }
          }
          else {
            /** if language is not set returns the array data_widget with locale_dirs and an empty array for result */
            $opt['data_widget'] = [
              'locale_dirs' => [],
              'result' => []
            ];
          }
          $res['paths'][] = $opt;
        }
      }
    }
    $res['success'] = true;
  }
}
return $res;
