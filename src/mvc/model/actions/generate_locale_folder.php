<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 09/03/18
 * Time: 17.26
 */

$success = false;
if ( !empty( $id_option = $model->data['id_option'])){

  $o = $model->inc->options->option($id_option);

  $parent = $model->inc->options->parent($id_option);

  $to_explore = constant($parent['code']) . $o['code'];
  $locale_dir = dirname($to_explore) . '/locale';

  //locale dirs existing before this configuration
  $languages_old = array_map(function($a){
    return basename($a);
  }, \bbn\file\dir::get_dirs($locale_dir)) ? : [];
//die(var_dump($languages_old, $model->data['languages']));

  $new_langs = array_diff($model->data['languages'], $languages_old);
  if ( !empty($new_langs) ){
    $translation = new \bbn\appui\i18n($model->db);
    $pp = $translation->get_parser();
    foreach ( $new_langs as $lang ){
      $path = $locale_dir . '/' . $lang . '/LC_MESSAGES';
      if ( \bbn\file\dir::create_path($path) ){
        Gettext\Generators\Po::toFile($pp, $path . '/'.$o['text'].'.po');
        Gettext\Generators\Mo::toFile($pp, $path . '/'.$o['text'].'.mo');
        $success = true;
      }
    }
  }

  return [
    'path' => $to_explore,
    'new_dir' => $new_langs,
    'success' => $success
  ];

}