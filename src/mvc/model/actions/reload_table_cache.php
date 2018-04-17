<?php
$success = false;
if ( $model->data['id_option'] ){
  $res = $model->get_cached_model(APPUI_I18N_ROOT.'page/data/strings_table', [
    'id_option' => $model->data['id_option'],
    'routes' => $model->data['routes']
    ], true);

  $success = true;
  return [
    'res' => $res,
    'success' => $success
  ];
}