<?php
$success = false;
  if ($id_option = $model->data['id_option'] ){

  if ( isset($model->data['id_project'] ) && ($model->data['id_project'] === 'options') ){

    $data_widget = $model->getModel(APPUI_I18N_ROOT.'options/options_data', [
      'root' => APPUI_I18N_ROOT,
      'res' => ['success' => true],
      'id_project' => $model->data['id_project'],
      'id_option' => $model->data['id_option'] ?? false
    ], true);

    $data_widget = $data_widget['data']['data'][0]['data_widget'];
  }
  else {
    //remake the cache of the widget
    $translation = new \bbn\Appui\I18n($model->db, $model->data['id_project']);

    $translation->cacheSet($id_option, 'get_translations_widget',
      $translation->getTranslationsWidget($model->data['id_project'],$id_option)
    );
    
    //return the new cache of the widget
    $data_widget = $translation->cacheGet($id_option, 'get_translations_widget');


  }
  $success = true;
  return [
    'data_widget' => $data_widget,
    'success' => $success
  ];
}
