
<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 12/12/17
 * Time: 15.13
 */


/** @todo this $model should set the cached model, I need help to do it. Now the cached model is created when opening
 * strings tab*/
//called when the button to search for new string in a path is clicked and from
// internationalization/page/path_translations/ to open the tab of the strings in the path


if ( empty($model->data['language']) ){
  $model->data['language'] = $model->inc->options->get_prop($model->data['id_option'], 'language');
}

if (
  ($id_option = $model->data['id_option']) &&
  ($o = $model->inc->options->option($id_option)) &&
  !empty($o['language']) &&
  ($parent = $model->inc->options->parent($id_option)) &&
  defined($parent['code']) &&
  ($source_language = $model->data['language'])
){
  /**instantiate $i18n to the class appui\i18n*/
  $i18n = new \bbn\appui\i18n($model->db);

  $strings = $i18n->get_translations_strings($model->data['id_option'],$model->data['language'], $model->data['languages']);
  
  return $strings;
}
