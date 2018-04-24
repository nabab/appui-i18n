<?php
/**
 * Created by PhpStorm.
 * User: bbn
 * Date: 24/04/18
 * Time: 12.51
 */
if ( !empty($model->data['id_option']) && !empty($model->data['language']) ){
  $success = false;
  if( $cfg = $model->inc->options->get_cfg() ){
    die(var_dump($cfg));
  }
}