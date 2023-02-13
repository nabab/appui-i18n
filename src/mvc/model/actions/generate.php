<?php
/*
 * Describe what it does!
 *
 **/


/**
 * @var \bbn\Mvc\Model $model
 * @var array $o The option for the repository to explore
 * @var array $parent The root of the repo where the code is the root constant
 */
use Gettext\Translations;

if (($o = $model->data['id_option'])
  && ($idProject = $model->data['id_project'])
  && ($language = $model->data['language'])
) {

  $translation = new \bbn\Appui\I18n($model->db, $idProject === 'options' ? null : $idProject);
  if ($translation->generateFiles($model->data['id_option'], $model->data['languages'])) {
    /** @var array The data of the table in cache */
    $strings = $translation->cacheGet($model->data['id_option'], 'get_translations_table');
    /** @var array The data of the widget in the cache*/
    $widget  = $translation->cacheGet($model->data['id_option'], 'get_translations_widget');
    return [
      'json' => $json,
      'widget' => $widget ?? null,
      'no_strings' => $no_strings,
      'new_dir' => $new_dir,
      'ex_dir' => $ex_dir,
      'path' => $to_explore,
      'locale' => $locale_dir,
      'languages' => $languages,
      'success' => $success,
      'strings' => empty($strings) ? [] : $strings['strings']
    ];
  }

}
