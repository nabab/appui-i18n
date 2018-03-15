<?php


if ( !empty($model->data['id_option']) ){

  return $model->get_model(APPUI_I18N_ROOT.'languages_tabs/data/widgets');
}