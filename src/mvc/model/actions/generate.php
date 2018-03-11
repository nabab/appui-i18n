<?php
/*
 * Describe what it does!
 *
 **/

/** @var $this \bbn\mvc\model*/

if (
  isset($model->data['id']) &&
  ($o = $model->inc->options->option($model->data['id'])) &&
  ($parent = $model->inc->options->parent($o['id'])) &&
  defined($parent['code'])
){
  //$to_explore is the directory to explore for strings
  $to_explore = constant($parent['code']).$o['code'];
  $locale_dir = dirname($to_explore).'/locale';
  $languages = array_map(function($a){
    return basename($a);
  }, \bbn\file\dir::get_dirs($locale_dir));
  $translation = new \bbn\appui\i18n($model->db);
  $res = $translation->analyze_folder($to_explore, true);
  $pp = $translation->get_parser();
  foreach ( $languages as $lang ){
    $path = $locale_dir.'/'.$lang.'/LC_MESSAGES';
    if ( \bbn\file\dir::create_path($path) ){
      Gettext\Generators\Po::toFile($pp, $path.'/zzzappui-styles.po');
      Gettext\Generators\Mo::toFile($pp, $path.'/appui-styles.mo');
    }
  }
  //die(var_dump(is_dir($to_explore), $to_explore, $res, $pp));
  return [
    'path' => $to_explore,
    'locale' => $locale_dir,
    'languages' => $languages,
  ];
}