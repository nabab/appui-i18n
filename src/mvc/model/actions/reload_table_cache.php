<?php
$success = false;
if ( $model->data['id_option'] ){
  $res = $model->get_cached_model(APPUI_I18N_ROOT.'languages_tabs/data/strings_table', ['id_option' => $model->data['id_option'] ], true);
  $success = true;
  return [
    'res' => $res,
    'success' => $success
  ];
}