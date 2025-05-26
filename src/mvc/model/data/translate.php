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

  $res = [];
  foreach ($list as $i => $l) {
    $res[$l] = [
      'expression' => $l
    ];
    foreach ($model->data['langs'] as $lang) {
      $res[$l][$lang] = [
        'translation' => $i18nCls->getTranslation($l, $opt['language'], $lang) ?: '',
      ];
      if (($i < 10)
        && ($api = $i18nCls->apiTranslate($l, $opt['language'], $lang))
      ) {
        $res[$l][$lang] = X::mergeArrays($res[$l][$lang], [
          'suggestion' => $api['translated'] ?: '',
          'alternatives' => $api['alternatives'] ?: []
        ]);
      }
      else {
        $res[$l][$lang] = X::mergeArrays($res[$l][$lang], [
          'suggestion' => '',
          'alternatives' => []
        ]);
      }
    }
  }

  return [
    'success' => !is_null($notTranslated),
    'expressions' => array_values($res)
  ];
}

return ['success' => false];