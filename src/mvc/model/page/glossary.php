<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 14/12/17
 * Time: 13.05
 */

if ( !empty($model->data['source_lang']) && !empty($model->data['translation_lang']) ){
  return [
    'pageTitle' => 'From '.$model->data['source_lang_name'].' to '.$model->data['lang_name']
  ];
}
