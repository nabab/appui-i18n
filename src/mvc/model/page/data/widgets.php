<?php
/** @var $this \bbn\mvc\model*/

use Gettext\Translations;
/** @var $projects the array of projects and path from the db */
$projects = $model->get_model(APPUI_I18N_ROOT.'page')['projects'];
$success = false;
$result = [];
$timer = new \bbn\util\timer();
$timer->start();

/**  only if the property language is set the cached model of the widget will be created */
if (
  ($id_option = $model->data['id_option']) &&
  ($o = $model->inc->options->option($id_option)) &&
  !empty($o['language']) &&
  ($parent = $model->inc->options->parent($id_option)) &&
  defined($parent['code']) &&
  isset($o['language'])
){
  $domain = $o['text'];

  /** @var $to_explore the path to explore */
  $to_explore = constant($parent['code']).$o['code'];
  /** @var $locale_dir the path to locale dir */
  $locale_dir = dirname($to_explore).'/locale';

  /** @var $dirs scans dirs existing in locale folder for this path */
  $dirs = scandir($locale_dir, 1);
  /** @var (array)$languages dirs in locale folder*/
  $languages = array_map(function($a){
    return basename($a);
  }, \bbn\file\dir::get_dirs($locale_dir)) ?: [];

  /** @var  $translation instantiate the class appui\i18n */
  $translation = new \bbn\appui\i18n($model->db);


  $new = 0;

  /** @var  $id_project gets the id of the project from id_option */
  $id_project = $translation->get_id_project($id_option, $projects);


  $i = 0;

  /**var (array) the languages found in locale dir */
  if ( !empty($languages) ){
    $result = [];
    foreach ( $languages as $lng ){
      /** the root to file po & mo */
      $po = $locale_dir.'/'.$lng.'/LC_MESSAGES/'.$domain.'.po';
      $mo = $locale_dir.'/'.$lng.'/LC_MESSAGES/'.$domain.'.mo';
      /** if a file po already exists takes its content */
      if ( is_file($po) ){
        if ( $translations = Gettext\Translations::fromPoFile($po) ){
          /** $result[$lng] contains num: the total number of strings in the path, num_translations: the number of strings translated in $lng */
          $result[$lng] = [
            'num' => $translations->count(),
            'num_translations' => $translations->countTranslated(),
            'lang' => $lng
          ];
        }

      }
      /** if the file po for the $lng doesn't exist $result is an empty object */
      else{
        $result[$lng] = [
          'num' => 0,
          'num_translations' => 0,
          'lang' => $lng
        ];
        $translations = new Gettext\Translations();
      }
    }
  }
  $i++;
  $success = true;

}
return [
  'locale_dirs' => $languages,
  'result' => $result,
  'success' => $success,
  'time' => $timer->measure()
];