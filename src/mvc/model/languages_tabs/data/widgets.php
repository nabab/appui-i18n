<?php
/**
 * Created by PhpStorm.
 * User: bbn
 * Date: 19/03/18
 * Time: 16.01
 */


use Gettext\Translations;
//get the array projects
$projects = $model->get_model(APPUI_I18N_ROOT.'languages_tabs')['projects'];
$success = false;
$result = [];
$timer = new \bbn\util\timer();
$timer->start();

//the cached model will be created only if the source language of the path is defined
if (
  ($id_option = $model->data['id_option']) &&
  ($o = $model->inc->options->option($id_option)) &&
  !empty($o['language']) &&
  ($parent = $model->inc->options->parent($id_option)) &&
  defined($parent['code']) &&
  isset($o['language'])
){
  $domain = $o['text'];

  $to_explore = constant($parent['code']).$o['code'];
  //take locale dirs
  $locale_dir = dirname($to_explore).'/locale';

  $dirs = scandir($locale_dir, 1);
  //create the array $languages basing on locale dirs in the path
  $languages = array_map(function($a){
    return basename($a);
  }, \bbn\file\dir::get_dirs($locale_dir)) ?: [];
  //instantiate the class appui\i18n
  $translation = new \bbn\appui\i18n($model->db);


  $new = 0;
  //get the id of the project from id_option
  $id_project = $translation->get_id_project($id_option, $projects);


  //instantiate the class appui\project
  $project = new \bbn\appui\project($model->db, $id_project);
  /**@var (string) the source lang of the project*/
  $project_lang = $project->get_lang();

  //$r is the string, $val is the array of files in which this string is contained
  $i = 0;

  /**var (array) the languages found in locale dir*/
  if ( !empty($languages) ){
    $result = [];
    foreach ( $languages as $lng ){
      //the name of po and mo files
      $po = $locale_dir.'/'.$lng.'/LC_MESSAGES/'.$domain.'.po';
      $mo = $locale_dir.'/'.$lng.'/LC_MESSAGES/'.$domain.'.mo';
      //takes the content of the po file using Gettext\Translations class
      if ( is_file($po) ){
        if ( $translations = Gettext\Translations::fromPoFile($po) ){
          //result contains num: the total number of strings in the path, num_translations: the number of strings translated in $lng
          $result[$lng] = [
            'num' => $translations->count(),
            'num_translations' => $translations->countTranslated(),
            'lang' => $lng
          ];
        }

      }
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