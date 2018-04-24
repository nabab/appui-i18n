<?php
$success = false;
if ( $model->data['id_option'] ){
  if ( isset($model->data['id_project'] ) && ($model->data['id_project'] === 'options') ){
    $data_widget = $model->get_cached_model(APPUI_I18N_ROOT.'options/options_data', [
      'root' => APPUI_I18N_ROOT,
      'res' => ['success' => true],
      'id_project' => $model->data['id_project']
    ], true);

    $data_widget = $data_widget['data']['data'][0]['data_widget'];
  }
  else {
    $data_widget = $model->get_cached_model(APPUI_I18N_ROOT.'page/data/widgets', ['id_option' => $model->data['id_option'] ], true);
  }
  $success = true;
  return [
    'data_widget' => $data_widget,
    'success' => $success
  ];
}