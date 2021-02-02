
<?php
/**
 * Created by BBN Solutions. Get the array of strings found in the path
 * User: Loredana Bruno
 * Date: 12/12/17
 * Time: 15.13
 */



if ( empty($model->data['language']) ){
  $model->data['language'] = $model->inc->options->getProp($model->data['id_option'], 'language');
}

if (
  ($id_option = $model->data['id_option']) &&
  ($o = $model->inc->options->option($id_option)) &&
  !empty($o['language']) &&
  ($source_language = $model->data['language']) &&
  !empty($model->data['languages'])
){
  /** @var array instantiate $i18n to the class Appui\I18n*/
  $i18n = new \bbn\Appui\I18n($model->db,  $model->data['id_project']);

  $strings = $i18n->getTranslationsStrings($model->data['id_option'],$model->data['language'], $model->data['languages']);
  
  return $strings;
}
