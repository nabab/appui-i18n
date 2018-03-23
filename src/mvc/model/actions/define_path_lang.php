<?php

//set the property (source) language for the path, if a cached model of the widget exists it deletes it and creates a new one
if ( isset( $model->data['language'] ) && $model->data['id_option'] ) {
  $model->inc->options->set_prop($model->data['id_option'], ['language' => $model->data['language']]);

  $data_widget = $model->get_cached_model(APPUI_I18N_ROOT.'languages_tabs/data/widgets',
    ['id_option' => $model->data['id_option'] ], true );

  //delete_cached_model doesn't work
  $model->get_cached_model(APPUI_I18N_ROOT.'languages_tabs/data/strings_table',
    ['id_option' => $model->data['id_option'] ], true);

  return [
    'data_widget' => $data_widget,
    'success' => true
  ];
}