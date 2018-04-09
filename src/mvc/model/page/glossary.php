<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 14/12/17
 * Time: 13.05
 */

if ( !empty($model->data['source_lang']) && !empty($model->data['source_lang']) ){

  return [
    'pageTitle' => $model->data['lang_name'].' translations from '.$model->data['source_lang_name']
  ];
}