<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 28/11/17
 * Time: 12.51 */

if( !empty($id_option = $ctrl->arguments[0])){
  //works
  $cached_model  = $ctrl->get_cached_model(APPUI_I18N_ROOT.'languages_tabs/data/widgets',['id_option' => $ctrl->arguments[0]] );
  $ctrl->add_data($cached_model)->combo('$pageTitle', true);
}