<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 28/11/17
 * Time: 12.51 */

if( !empty($ctrl->arguments[0]) ){
  $ctrl->obj->url = APPUI_I18N_ROOT.'languages/glossary_tab/'.$ctrl->arguments[0];
  $primary = $ctrl->get_model('internationalization/languages')['primary'];
  foreach ( $primary as $p => $val ){
    if ($primary[$p]['code'] === $ctrl->arguments[0] ){
      $lang_name =  $primary[$p]['text'];
    }
  }
  //to send $ctrl->arguments[0] to the $model
  $ctrl->add_data(['lang' => $ctrl->arguments[0], 'lang_name' => $lang_name])->combo('$pageTitle', true);
}
