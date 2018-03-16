<?php

if ( isset($ctrl->post['id_option']) ){
  $ctrl->delete_cached_model(APPUI_I18N_ROOT.'languages_tabs/data/widgets', ['id_option'=> $ctrl->post['id_option']]);
  $ctrl->action();
}