<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 14/12/17
 * Time: 13.05
 */

//basing on $ctrl->arguments[0] from the bbn.fn.link
if ( !empty($model->data['lang']) ){

  return [
    'pageTitle' => $model->data['lang_name'].'\'s translations',
  ];
}
