<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 14/12/17
 * Time: 13.05
 */

/* @var string ID of the path to analyze is expected */


if ( !empty($model->data['id_option']) ){
  return [

    'pageTitle' =>  $model->inc->options->text($model->data['id_option']),

  ];
}
