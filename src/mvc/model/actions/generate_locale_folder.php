<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 09/03/18
 * Time: 17.26
 */

$success = false;
if (
  ($id_option = $model->data['id_option']) &&
  ($o = $model->inc->options->option($id_option)) &&
  !empty($o['language']) &&
  ($parent = $model->inc->options->parent($id_option)) &&
  defined($parent['code']) &&
  isset($o['language'])
){

  $to_explore = constant($parent['code']) . $o['code'];
  $locale_dir = dirname($to_explore) . '/locale';
  if ( $data = $model->get_cached_model(APPUI_I18N_ROOT.'languages_tabs/data/strings_table', ['id_option' => $id_option], true) ) {
    //locale dirs existing before this configuration
    $languages_old = $data['languages'];



    $new_langs = array_diff($model->data['languages'], $languages_old);

    if (!empty($new_langs)) {
      foreach ($new_langs as $lang) {

        bbn\file\dir::create_path( $locale_dir . '/' . $lang . '/LC_MESSAGES');
          //create en empty locale folder for each new language
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