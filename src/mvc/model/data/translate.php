<?php
use bbn\X;
use \bbn\Appui\I18n;

if ($model->hasData(['project', 'path', 'langs'], true)
  && ($opt = $model->inc->options->option($model->data['path']))
  && !empty($opt['language'])
) {
  $i18nCls = new I18n($model->db, $model->data['project'], [
    'service' => BBN_I18N_API_SERVICE,
    'url' => BBN_I18N_API_URL,
    'sourceLang' => $opt['language'],
    'alternatives' => 3
  ]);
  if ($model->hasData('expressions', true)) {
    $list = $model->data['expressions'];
  }
  else {
    $list = [];
    foreach ($model->data['langs'] as $lang) {
      if ($notTranslated = $i18nCls->getNotTranslated($model->data['path'], $lang)) {
        $list = X::mergeArrays($list, $notTranslated);
      }
    }

  }

  $expressions = array_map(function($s, $i) use ($i18nCls, $model, $opt) {
    $r = [
      'expression' => $s
    ];
    foreach ($model->data['langs'] as $lang) {
      $r[$lang] = [
        'translation' => $i18nCls->getTranslation($s, $opt['language'], $lang) ?: '',
        'suggestions' => []
      ];
      if (($i < 20)
        && ($api = $i18nCls->apiTranslate($s, $opt['language'], $lang))
      ) {
        $r[$lang]['suggestions'] = X::mergeArrays([$api['translated']], $api['alternatives'] ?: []);
      }
    }

    return $r;
  }, $list, array_keys($list));

  return [
    'success' => !is_null($notTranslated),
    'expressions' => $expressions
  ];
}

return ['success' => false];