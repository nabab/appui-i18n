<?php
/*
 * Describe what it does!
 *
 **/



/** @var $this \bbn\mvc\model*/



if ( $o = $model->data['id_option'] ) {
  $success = false;
  $parent = $model->inc->options->parent($o);
  $code = $model->inc->options->code($o);
  $locale = '';
  if ( constant($parent['code']) === BBN_LIB_PATH ){
    $locale = constant($parent['code']).$code.'locale';
  }
  else if ( constant($parent['code']) === BBN_APP_PATH  ){
    if ( $code === 'mvc/' ){
      $locale = constant($parent['code']).'locale';
    }
    else{
      $locale = constant($parent['code']).$code.'locale';
    }
  }
  if ( is_dir($locale) ){
    $success =  \bbn\file\dir::delete($locale, true);
  }
	return [
    'success' => $success,
    
  ];
}
