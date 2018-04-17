<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 14/12/17
 * Time: 13.05
 */

/* @var string ID of the path to analyze is expected */

if ( !empty($model->data['id_option']) ){
  $model->get_cached_model(APPUI_I18N_ROOT.'page/data/widgets', [
    'id_option' => $model->data['id_option'],
  ], 1);

  $res = $model->get_cached_model(APPUI_I18N_ROOT.'page/data/strings_table', [
    'id_option' => $model->data['id_option'],
    'routes' => $model->data['routes']
  ], 0);
//die(var_dump($res));
  return [
    'res' => $res,
    'pageTitle' =>  $model->inc->options->text($model->data['id_option']),
  ];
}
