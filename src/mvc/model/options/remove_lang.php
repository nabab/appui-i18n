<?php
/**
 * Created by Loredana Bruno.
 * User: bbn
 * Date: 24/04/18
 * Time: 11.42
 */

if ( !empty($model->data['id_option']) ){
  $success = false;

  /** @var (array) $cfg all cfg of this option*/
  $cfg = $model->inc->options->get_cfg( $model->data['id_option'] );
  unset ($cfg['i18n']);
  /** set the cfg of the option without the property i18n */
  if ( $model->inc->options->set_cfg($model->data['id_option'] , $cfg) ){
    $success = true;
  }
  /** delete the model of the option in cache */
  if ( $data = $model->get_cached_model(APPUI_I18N_ROOT.'options/options_data', [
    'root' => APPUI_I18N_ROOT,
    'res' => ['success' => true],
    'id_project' => $model->data['id_project']
  ], true) ){
    $success = true;
  }
  return[
    'success' => $success
  ];
}
