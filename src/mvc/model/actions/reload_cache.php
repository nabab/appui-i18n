<?php

if ( $model->data['id_option'] ){
  return $model->get_cached_model(APPUI_I18N_ROOT.'languages_tabs/data/widgets',['id_option' => $model->data['id_option'] ] );
}