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
  ], 1);

  if ( !empty($model->data['id_project'] ) && ( $model->data['id_project'] === 'options') ){
    /**case project options*/
    /** @var  $cfg get the property i18n from option cfg to send it to the find_options*/
    if ( $cfg = $model->inc->options->get_cfg($model->data['id_option']) ){
      if ( !empty($cfg['i18n']) ){
        $model->data['language'] = $cfg['i18n'];

        $res = $model->get_cached_model(APPUI_I18N_ROOT.'options/find_options', [
          'id_option' => $model->data['id_option'],
          'language' => $model->data['language']

        ], true);
      }
    }
  }

  return [
    'res' => $res,
    'pageTitle' =>  $model->inc->options->text($model->data['id_option']),
  ];
}
