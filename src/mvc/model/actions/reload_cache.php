<?php

die(var_dump($model->data));
if ( !empty($model->data['id_option']) && !empty($model->data['cached_model']) ){
  return [
    'success' => true,
    'cached_model' => $model->data['cached_model']
  ];
}