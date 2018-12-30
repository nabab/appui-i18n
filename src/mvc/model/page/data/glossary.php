<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 17/01/18
 * Time: 12.35
 */

 
 // data for glossary table
if ( !empty($model->data['limit']) &&  !empty($model->data['data']['translation_lang']) && !empty($model->data['data']['source_lang']) ){

  $trans_lang_code = $model->data['data']['translation_lang'];
  $source_code = $model->data['data']['source_lang'];


  $grid = new \bbn\appui\grid($model->db, $model->data, [
    'tables' => ['bbn_i18n'],
    'fields' => [
      'bbn_i18n.exp',
      'idExp' => 'bbn_i18n.id',
      'translation' => 'expression'
      
    ],
    'join' => [[
      'type' => 'left',
      'table' => 'bbn_i18n_exp',
      'on' => [
        'logic' => 'AND',
        'conditions' => [[
          'field' => 'bbn_i18n_exp.id_exp',
          'operator' => 'eq',
          'exp' => 'bbn_i18n.id'
        ],[
          'field' => 'bbn_i18n_exp.lang',
          'operator' => 'eq',
          'value' => $trans_lang_code
        ]]
      ],
    ]],
    'filters' => [[
      'field' => 'bbn_i18n.lang',
      'operator' => '=',
      'value' => $source_code
    ]],
    'group_by' => 'bbn_i18n.id',
  ]);

  if ( $grid->check() ){
  
    return $grid->get_datatable();
  }
}
