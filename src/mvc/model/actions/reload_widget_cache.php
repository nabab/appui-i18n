<?php
$success = false;
if ( $model->data['id_option'] ){
  $data_widget = $model->get_cached_model(APPUI_I18N_ROOT.'languages_tabs/data/widgets', ['id_option' => $model->data['id_option'] ], true);

  $success = true;
  return [
    'data_widget' => $data_widget,
    'success' => $success
  ];
}