<?php
use bbn\X;
use bbn\Appui\Project;
use bbn\Appui\I18n;

if ($model->hasData(['project', 'path'], true)) {
  $path = $model->data['path'];
  $project = $model->data['project'];
  $isOptions = $model->data['project'] === 'options';
  $i18nCls = new I18n($model->db, $project);
  $projectCls = new Project($model->db, $project);
  $primaries = $projectCls->getPrimariesLangs() ?: [];
  $res = [
    'project' => $project,
    'id' => $path,
    'primaries' => $primaries
  ];
  if ($opt = $model->inc->options->option($path)) {
    $parent = $model->inc->options->option($opt['id_parent']);
    $res['title'] = $parent['text'].'/'.$opt['text'];
    $res['language'] = $opt['language'] ?? '';
    $res['code'] = $opt['code'];
    $res['bcolor'] = $opt['bcolor'] ?? '';
    $res['fcolor'] = $opt['fcolor'] ?? '';
    if ($isOptions) {
      $res['languages'] = array_map(
        fn($l) => X::getField($primaries, ['code' => $l], 'id'),
        $model->inc->options->findI18nLocales()
      );
      if ((($parent['code'] === 'app')
          && ($opt['code'] === 'main'))
        || (($parent['code'] === 'lib')
          && (str_starts_with($opt['code'], 'appui-')))
      ) {
        if ($i18nCls->cacheHas($opt['id'], 'get_options_translations_widget')) {
          $res = X::mergeArrays($res, $i18nCls->cacheGet($opt['id'], 'get_options_translations_widget'));
        }
        else {
          $res = X::mergeArrays($res, $i18nCls->getOptionsTranslationsWidget($opt['id']) ?: []);
        }
      }
    }
    else {
      $res['languages'] = $projectCls->getLangsIds();
      if (!empty($opt['language'])) {
        if ($i18nCls->cacheHas($opt['id'], 'get_translations_widget')) {
          $res = X::mergeArrays($res, $i18nCls->cacheGet($opt['id'], 'get_translations_widget'));
        }
        else {
          $res = X::mergeArrays($res, $i18nCls->getTranslationsWidget($opt['id']) ?: []);
        }
      }
    }
  }

  return $res;
}