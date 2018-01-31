<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 28/11/17
 * Time: 12.51 */




if( !empty($ctrl->arguments[0])){
  $ctrl->obj->url = APPUI_I18N_ROOT.'languages/strings_tab/'.$ctrl->arguments[0];
  //to send $ctrl->arguments[0] to the $model
  $ctrl->add_data(['id_option' => $ctrl->arguments[0]])->combo('$pageTitle', true);
}
