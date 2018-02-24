<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 28/11/17
 * Time: 12.51 */


if( !empty($ctrl->arguments[0]) && !empty($ctrl->arguments[1]) ){
  $ctrl->data['translation_lang'] = $ctrl->arguments[1];
  $ctrl->data['source_lang'] = $ctrl->arguments[0];

  $ctrl->obj->url = APPUI_I18N_ROOT.'languages/glossary_tab/'.$ctrl->arguments[1];
  $primary = $ctrl->get_model('internationalization/languages')['primary'];
  foreach ( $primary as $p => $val ){
    if ($primary[$p]['code'] === $ctrl->arguments[1] ){
      $lang_name =  $primary[$p]['text'];
    }
  }
  //to send $ctrl->arguments[0] to the $model
  $ctrl->add_data(
    [
      'translation_lang' => $ctrl->arguments[1], 
     	'source_lang' => $ctrl->arguments[0], 
     	'lang_name' => $lang_name
    ])->combo('$pageTitle', true);
}