<?php
use bbn\Appui\I18n;

if ($model->hasData(['project', 'option'], true)) {
  $project = $model->data['project'];
  $option = $model->data['option'];
  $opt = $model->inc->options->option($option);
  $parentOpt = $model->inc->options->option($opt['id_parent']);
  $isOptions = ($project === 'options');
  $parentCode = $parentOpt['code'] ?: (!empty($parentOpt['alias']['code']) ? $parentOpt['alias']['code'] : '');
  $translation = new I18n($model->db, $isOptions ? null : $project);

  //if the table has no cache it creates cache
  if (!empty($project)
    && (!empty($model->data['force'])
      || ((!$isOptions
          && !$translation->cacheHas($option, 'get_translations_table'))
        || ($isOptions
          && !$translation->cacheHas($option, 'get_options_translations_table'))
        ))
  ) {
    if ($isOptions) {
      $translation->getOptionsTranslationsTable($option);
    }
    else {
      $translation->getTranslationsTable($project, $option);
    }
  }

  $res = $translation->cacheGet($option, $isOptions ? 'get_options_translations_table' : 'get_translations_table');
  $primaries = $translation->getPrimariesLangs();
  $t = $parentCode . (!empty($parentCode) ? '/' : '') . $opt['code'];
  return $model->addData([
    'primary' => $primaries,
    'id_project' => $project,
    'res' => $res,
    'pageTitle' => $isOptions ? \bbn\X::_('Options - %s', $t) : $t
  ])->data;
}
