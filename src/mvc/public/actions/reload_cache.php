<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 21/02/18
 * Time: 10.53
 */



if ( !empty($ctrl->post['id_option']) ){
  $ctrl->delete_cached_model(APPUI_I18N_ROOT.'languages_tabs/data/widgets', ['id_option'=> $ctrl->post['id_option']]);



  $ctrl->action();
}
