<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 14/12/17
 * Time: 13.05
 */

/* @var string ID of the path to analyze is expected */
if ($model->hasData(['project', 'option'], true)) {
  $project = $model->data['project'];
  $option = $model->data['option'];
  $isOptions = ($project === 'options');

  /** @var  $translation instantiate the class Appui\I18n*/
  $translation = new \bbn\Appui\I18n($model->db, $isOptions ? null : $project);

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
  return $model->addData([
    'primary' => $primaries,
    'id_project' => $project,
    'res' => $res,
    'pageTitle' => $isOptions ? \bbn\X::_('Options - %s', $model->inc->options->text($option)) : $model->inc->options->text($option)
  ])->data;
}
